<?php

namespace App\Http\Middleware;

use App\Services\StoreContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveStore
{
    public function __construct(protected StoreContext $storeContext)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin-login');
        }

        if ($this->storeContext->hasGlobalStoreAccess($user)) {
            return $next($request);
        }

        if ($this->storeContext->requiresStoreSelection($user)) {
            return redirect()->route('choose-store');
        }

        return $next($request);
    }
}
