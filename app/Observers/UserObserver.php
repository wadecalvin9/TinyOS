<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    public function created(User $user)
    {
        // Main folder named by user ID
        $root = $user->id;

        // Create the main user directory
        Storage::disk('ftp')->makeDirectory($root);

        // Extra OS-like folders
        $subfolders = [
            'Desktop',
            'Documents',
            'Downloads',
            'Pictures',
            'Music',
            'Videos',
            'System'
        ];

        // Create subfolders inside the user directory
        foreach ($subfolders as $folder) {
            Storage::disk('ftp')->makeDirectory($root . '/' . $folder);
        }
    }
}
