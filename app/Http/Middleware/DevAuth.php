<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DevAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession()) {
            $authenticated = $request->session()->get('dev.authenticated', false);
            if ($authenticated === true || $authenticated === 1 || $authenticated === '1') {
                return $next($request);
            }
        }

        return redirect()->route('dev.login');
    }
}
