<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (($user->status ?? 'Active') !== 'Active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $loginRoute = $request->routeIs('applicant-dashboard', 'frontend-login')
                || str_starts_with($request->path(), 'applicant')
                ? 'login'
                : 'admin-login';

            return redirect()
                ->route($loginRoute)
                ->with(
                    'login_error_message',
                    'Your account has been blocked. Contact your administrator.'
                );
        }

        return $next($request);
    }
}
