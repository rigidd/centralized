<?php

namespace App\Http\Controllers\Auth\MFAChallenge;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Date;
use LaravelWebauthn\Facades\Webauthn;
use LaravelWebauthn\Http\Requests\WebauthnLoginRequest;
use Str;

class VerifyWebAuthnKeyController extends Controller
{
    public function __invoke(WebauthnLoginRequest $request): JsonResponse
    {

        $user = $request->user();

        $confirmed = Webauthn::validateAssertion($user, $request->toArray());

        if ($confirmed) {
            $request->session()->put('auth.mfa_passed', Date::now()->unix());
            $mfa_verification_token = Str::random(60);
            $request->session()->put('auth.mfa_verification_token', $mfa_verification_token);

            return response()->json(['status' => 'mfa_verified', 'mfa_verification_token' => $mfa_verification_token]);
        }

        return response()->json(['error' => 'mfa_failed'], 422);
    }
}
