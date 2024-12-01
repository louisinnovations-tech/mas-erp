<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }else{
            // Get the current host (subdomain)
            $host = $request->getHost();
            if ($host === config('app.client_url') && !$request->user->hasRole('client')) {
                return redirect()->route('login')->withErrors('Unauthorized access to client portal.');
            }
            if ($host === config('app.employee_url') && $request->user->hasRole('client')) {
                return redirect()->route('login')->withErrors('Unauthorized access to employee portal.');
            }
        }
    }
}
