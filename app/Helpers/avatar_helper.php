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
    function avatar_url(?string $avatar): ?string
    {
        return AvatarStorageService::url($avatar);
    }
}
