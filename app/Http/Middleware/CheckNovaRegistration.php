<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckNovaRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip the license gate in automated-testing environments
        if (app()->environment(['testing', 'e2e'])) {
            return $next($request);
        }

        $isRegistered = Cache::get('nova_license_valid');

        if ($isRegistered === null) {
            Artisan::call('nova:check-license');
            $output = Artisan::output();
            $isRegistered = str_contains(strtolower($output), 'valid');

            // Only cache if valid — invalid results re-check on every request
            if ($isRegistered) {
                Cache::put('nova_license_valid', true, now()->addMinutes(10));
            }
        }

        if (! $isRegistered) {
            // Allow the support page and all nova-api requests through
            if (
                $request->is('nova/support-page') ||
                $request->is('nova-api/*')
            ) {
                return $next($request);
            }

            // For Inertia SPA navigation, redirect to support page
            if ($request->header('X-Inertia')) {
                return inertia()->location('/nova/support-page');
            }

            // For initial full page load
            return redirect('/nova/support-page');
        }

        return $next($request);
    }
}
