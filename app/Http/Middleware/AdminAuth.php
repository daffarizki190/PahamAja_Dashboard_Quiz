<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession()) {
            $authenticated = $request->session()->get('admin.authenticated', false);
            if ($authenticated === true || $authenticated === 1 || $authenticated === '1') {
                return $next($request);
            }
        }

        if ($request->cookie('admin_auth') === '1') {
            if ($request->hasSession()) {
                $request->session()->put('admin.authenticated', true);
            }

            return $next($request);
        }

        return redirect()->route('admin.login');
    }
}
