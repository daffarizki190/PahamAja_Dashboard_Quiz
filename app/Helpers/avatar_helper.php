<?php

use App\Services\AvatarStorageService;

if (!function_exists('avatar_url')) {
    /**
     * Convert a stored avatar value (local path or full Supabase URL) to a
     * fully-qualified URL suitable for use in <img src="...">.
     *
     * @param  string|null  $avatar   The value stored in employees.avatar
     * @return string|null
     */
    function avatar_url(?string $avatar, ?string $name = null): ?string
    {
        $url = AvatarStorageService::url($avatar);

        // If no avatar is set, or if the avatar is broken (like a /tmp/ file on Vercel),
        // fallback to a dynamic generated avatar based on the employee's name.
        if (!$url || \Illuminate\Support\Str::startsWith($url, 'http://localhost/tmp/')) {
            if ($name) {
                // Generate a beautiful, colorful avatar based on the name
                return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random&color=fff&size=256&bold=true';
            }
            return null;
        }

        return $url;
    }
}
