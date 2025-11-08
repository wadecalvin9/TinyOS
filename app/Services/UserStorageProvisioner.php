<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserStorageProvisioner
{
    /**
     * Create the user's root folder and default subfolders on the FTP disk.
     */
    public static function provision(User $user): void
    {
        $disk = Storage::disk('ftp');
        $root = self::userRoot($user); // e.g. 4

        try {
            if (!$disk->exists($root)) {
                $disk->makeDirectory($root);
            }

            $defaults = ['Desktop', 'Documents', 'Downloads', 'Pictures', 'Music', 'Videos'];

            foreach ($defaults as $dir) {
                $path = "$root/$dir";
                if (!$disk->exists($path)) {
                    $disk->makeDirectory($path);
                }
            }

            // Optional: starter README file
            $readme = "$root/Documents/README.txt";
            if (!$disk->exists($readme)) {
                $disk->put($readme, "Welcome, {$user->name}!\nThis is your FTP-backed storage.");
            }
        } catch (\Throwable $e) {
            Log::error('Provisioning storage failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the root folder path for a user.
     */
    public static function userRoot(User $user): string
    {
        return (string) $user->id; // just the user ID
    }
}
