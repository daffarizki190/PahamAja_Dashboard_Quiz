<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Handles avatar upload & deletion to Supabase Storage (S3-compatible).
 * Falls back to local 'public' disk if Supabase is not configured.
 */
class AvatarStorageService
{
    protected string $disk;

    public function __construct()
    {
        // Use supabase disk if configured, otherwise fall back to local public disk
        $this->disk = config('filesystems.disks.supabase.key') ? 'supabase' : 'public';
    }

    /**
     * Upload a new avatar file and return its public URL (or local path).
     */
    public function upload(UploadedFile $file): string
    {
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $mimeType  = $file->getMimeType() ?: 'image/jpeg';

        if ($this->disk === 'supabase') {
            Storage::disk('supabase')->put($filename, file_get_contents($file->getRealPath()), [
                'visibility'  => 'public',
                'ContentType' => $mimeType,
            ]);

            return rtrim(config('filesystems.disks.supabase.url'), '/') . '/' . $filename;
        }

    // Local fallback
        return $file->storeAs('avatars', $filename, 'public');
    }


    /**
     * Delete an avatar given its stored value (URL or local path).
     */
    public function delete(?string $avatar): void
    {
        if (!$avatar) {
            return;
        }

        if (Str::startsWith($avatar, 'http')) {
            // Extract just the filename from the public URL
            $filename = basename(parse_url($avatar, PHP_URL_PATH));
            Storage::disk('supabase')->delete($filename);
        } else {
            Storage::disk('public')->delete($avatar);
        }
    }

    /**
     * Convert a stored avatar value to a fully qualified public URL.
     * Returns null if avatar is empty.
     */
    public static function url(?string $avatar): ?string
    {
        if (!$avatar) {
            return null;
        }

        // Already a full URL (Supabase)
        if (Str::startsWith($avatar, 'http')) {
            return $avatar;
        }

        // Local path — use asset() equivalent
        return asset('storage/' . $avatar);
    }
}
