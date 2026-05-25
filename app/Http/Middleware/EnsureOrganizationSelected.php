<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !session()->has('active_organization_id')) {
            // Check if user has organizations
            $organizations = Auth::user()->organizations;

            if ($organizations->isEmpty()) {
                // This shouldn't happen for admin/panitia, but maybe for a new user
                return $next($request);
            }

            if ($organizations->count() === 1) {
                session(['active_organization_id' => $organizations->first()->id]);
                return $next($request);
            }

            // Redirect to organization selection page
            if (!$request->routeIs('organization.select') && !$request->routeIs('organization.set')) {
                return redirect()->route('organization.select');
            }
        }

        return $next($request);
    }
}
