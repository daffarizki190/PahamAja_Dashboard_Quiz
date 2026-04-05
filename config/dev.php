<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dev Team Password
    |--------------------------------------------------------------------------
    |
    | Password untuk mengakses halaman health monitor tim developer.
    | Set DEV_PASSWORD di Vercel Environment Variables.
    |
    */

    'password' => env('DEV_PASSWORD', ''),
];
