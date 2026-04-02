<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    public function upload(UploadedFile $file): string
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');
        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
