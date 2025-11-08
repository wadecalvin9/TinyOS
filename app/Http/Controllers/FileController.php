<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;

class FileController extends Controller
{
    private function disk()
    {
        return Storage::disk('ftp'); // your FTP disk
    }

    private function userRoot(): string
    {
        return (string) Auth::id(); // each user has their own root
    }

    private function sanitize(string $path = ''): string
    {
        $path = trim($path, "/\\\t\n\r\0\x0B");
        $path = str_replace(['..', "\\"], ['', '/'], $path); // prevent traversal
        $path = preg_replace('#/{2,}#', '/', $path);
        return ltrim($path, '/');
    }

    // List directory
    public function list(Request $request)
    {
        try {
            $rel = $this->sanitize($request->query('dir', ''));
            $base = $this->userRoot();
            $dir = $rel !== '' ? $base . '/' . $rel : $base;

            if (!$this->disk()->exists($dir)) {
                return response()->json([
                    'success' => false,
                    'error' => "Directory not found: $dir"
                ]);
            }

            $items = [];
            foreach ($this->disk()->listContents($dir, false) as $attr) {
                $name = basename($attr->path());
                $relativePath = ltrim(str_replace($base, '', $attr->path()), '/');

                if ($attr instanceof DirectoryAttributes) {
                    $items[] = [
                        'type' => 'dir',
                        'name' => $name,
                        'path' => $relativePath,
                    ];
                } elseif ($attr instanceof FileAttributes) {
                    $items[] = [
                        'type' => 'file',
                        'name' => $name,
                        'path' => $relativePath,
                        'size' => $attr->fileSize(),
                        'lastModified' => $attr->lastModified(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'cwd' => $rel,
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            Log::error('List failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Save file
public function saveToFtp(Request $request)
{
    try {
        $filename = $request->input('filename');
        $content = $request->input('content');

        // save to FTP logic
        // For example:
        Storage::disk('ftp')->put($filename, $content);

        return response()->json([
            'success' => true,
            'message' => 'File created successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}


    // Load file
    public function loadFromFtp(Request $request)
    {
        $rel = $this->sanitize($request->query('filename', ''));
        $path = $this->userRoot() . '/' . $rel;

        if (!$this->disk()->exists($path)) {
            return response()->json(['success' => false, 'error' => 'File not found'], 404);
        }

        $mime = $this->disk()->mimeType($path) ?? 'application/octet-stream';
        $raw = $this->disk()->get($path);

        return response()->json([
            'success' => true,
            'filename' => $rel,
            'mime' => $mime,
            'base64' => base64_encode($raw),
        ]);
    }

    // Make directory
    public function makeDir(Request $request)
    {
        $dir = $this->sanitize($request->validate(['dir' => 'required|string'])['dir']);
        $path = $this->userRoot() . '/' . $dir;

        try {
            if ($this->disk()->exists($path)) {
                return response()->json(['success' => false, 'error' => 'Already exists'], 422);
            }

            // Ensure parent directories exist
            $parentDir = dirname($path);
            if (!$this->disk()->exists($parentDir)) {
                $this->disk()->makeDirectory($parentDir);
            }

            $created = $this->disk()->makeDirectory($path);
            return response()->json(['success' => (bool)$created]);
        } catch (\Throwable $e) {
            Log::error('Make directory failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Rename file or folder
    public function rename(Request $request)
    {
        $data = $request->validate(['from' => 'required|string','to' => 'required|string']);
        $from = $this->userRoot() . '/' . $this->sanitize($data['from']);
        $to   = $this->userRoot() . '/' . $this->sanitize($data['to']);

        if (!$this->disk()->exists($from)) {
            return response()->json(['success' => false, 'error' => 'Source not found'], 404);
        }

        return response()->json(['success' => (bool)$this->disk()->move($from, $to)]);
    }

    // Delete file or folder
    public function delete(Request $request)
    {
        $data = $request->validate(['path' => 'required|string', 'type' => 'required|in:file,dir']);
        $path = $this->userRoot() . '/' . $this->sanitize($data['path']);

        try {
            if ($data['type'] === 'dir') {
                return response()->json(['success' => (bool)$this->disk()->deleteDirectory($path)]);
            }
            return response()->json(['success' => (bool)$this->disk()->delete($path)]);
        } catch (\Throwable $e) {
            Log::error('Delete failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
