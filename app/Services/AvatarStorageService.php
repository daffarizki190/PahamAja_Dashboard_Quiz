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
            try {
                $url = rtrim(config('filesystems.disks.supabase.url'), '/');
                $bucket = config('filesystems.disks.supabase.bucket', 'avatars');
                $key = config('filesystems.disks.supabase.secret') ?: config('filesystems.disks.supabase.key');

                $baseStorageUrl = Str::contains($url, '/storage/v1') ? $url : "{$url}/storage/v1";
                $endpoint = "{$baseStorageUrl}/object/{$bucket}/{$filename}";
                $publicUrl = "{$baseStorageUrl}/object/public/{$bucket}/{$filename}";

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                    'apikey' => $key,
                    'Content-Type' => $mimeType,
                ])->withBody(file_get_contents($file->getRealPath()), $mimeType)->post($endpoint);

                if ($response->successful()) {
                    return $publicUrl;
                }
                
                \Illuminate\Support\Facades\Log::error('Supabase HTTP upload failed: ' . $response->body());
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Supabase upload exception: ' . $e->getMessage());
            }

            // If Supabase fails or is misconfigured, Vercel cannot write to storage/app/public.
            // We must prevent a 500 crash by using /tmp on Vercel.
            if (env('VERCEL')) {
                $file->move('/tmp/avatars', $filename);
                return '/tmp/avatars/' . $filename;
            }
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
            try {
                $filename = basename(parse_url($avatar, PHP_URL_PATH));
                $url = rtrim(config('filesystems.disks.supabase.url'), '/');
                $bucket = config('filesystems.disks.supabase.bucket', 'avatars');
                $key = config('filesystems.disks.supabase.secret') ?: config('filesystems.disks.supabase.key');

                $baseStorageUrl = Str::contains($url, '/storage/v1') ? $url : "{$url}/storage/v1";
                $endpoint = "{$baseStorageUrl}/object/{$bucket}/{$filename}";

                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                    'apikey' => $key,
                ])->delete($endpoint);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Supabase delete exception: ' . $e->getMessage());
            }
        } else {
            if (!env('VERCEL') || !Str::startsWith($avatar, '/tmp')) {
                Storage::disk('public')->delete($avatar);
            }
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
