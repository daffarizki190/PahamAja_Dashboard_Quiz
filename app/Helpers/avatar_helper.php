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
        // generate a local, fully offline SVG avatar based on the employee's name.
        if (!$url || \Illuminate\Support\Str::contains($url, '/tmp/avatars/')) {
            if ($name) {
                // Get initials
                $words = explode(' ', trim($name));
                $initials = strtoupper(mb_substr($words[0], 0, 1));
                if (count($words) > 1) {
                    $initials .= strtoupper(mb_substr(end($words), 0, 1));
                }

                // Pick a vibrant, deterministic background color based on name
                $colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F97316', '#EF4444', '#06B6D4', '#EC4899', '#14B8A6'];
                $bg = $colors[abs(crc32($name)) % count($colors)];

                // Generate SVG
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="'.$bg.'"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-weight="bold" font-size="100">'.$initials.'</text></svg>';

                return 'data:image/svg+xml;base64,' . base64_encode($svg);
            }
            return null;
        }

        return $url;
    }
}
