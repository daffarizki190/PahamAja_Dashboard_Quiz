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

            return Storage::disk('supabase')->url($filename);
        }

        // Local fallback
        $path = $file->storeAs('avatars', $filename, 'public');
        return Storage::disk('public')->url($path);
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

        // Handle full URLs
        if (Str::startsWith($avatar, ['http://', 'https://'])) {
            // Force HTTPS if in production/vercel
            if ((config('app.env') === 'production' || env('VERCEL')) && Str::startsWith($avatar, 'http://')) {
                return Str::replaceFirst('http://', 'https://', $avatar);
            }
            return $avatar;
        }

        // Handle protocol-relative URLs (e.g., //example.com/image.jpg)
        if (Str::startsWith($avatar, '//')) {
            return (config('app.env') === 'production' || env('VERCEL') ? 'https:' : 'http:') . $avatar;
        }

        // Handle internal absolute paths
        if (Str::startsWith($avatar, '/')) {
            return asset($avatar);
        }

        // Local path fallback
        // On Vercel, this is usually problematic, so we prioritize Supabase URLs
        return asset('storage/' . $avatar);
    }
}
