<?php

namespace App\Http\Middleware;

use App\Support\InstallState;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationIsInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('installer.enabled', true) || InstallState::isInstalled()) {
            return $next($request);
        }

        if ($request->is('install') || $request->is('install/*')) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson() || $request->is('up')) {
            return new JsonResponse([
                'message' => 'Application is not installed yet.',
                'install_url' => url('/install'),
                'status' => 'install_required',
            ], 503);
        }

        return new RedirectResponse(route('install.show'));
    }
}