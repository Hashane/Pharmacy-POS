<?php

namespace Modules\Customer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureCustomerEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user('customer') ||
            ($request->user('customer') instanceof MustVerifyEmail &&
                ! $request->user('customer')->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: 'customer.verification.notice'));
        }

        return $next($request);
    }
}
