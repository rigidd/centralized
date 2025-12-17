<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MFAStrictVerificationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('auth.mfa_passed') || !$request->session()->get('auth.mfa_verification_token')) {
            return abort(403, 'MFA verification required.');
        }

        if (!$request->header('X-MFA-VERIFICATION-TOKEN')) {
            return abort(403, 'MFA verification token is missing.');
        }

        $token = $request->session()->get('auth.mfa_verification_token');
        if ($request->header('X-MFA-VERIFICATION-TOKEN') !== $token) {
            return abort(403, 'Invalid MFA verification token.');
        }

        $request->session()->forget('auth.mfa_verification_token');

        return $next($request);
    }
}
