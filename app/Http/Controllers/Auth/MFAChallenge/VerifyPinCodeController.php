<?php

namespace App\Http\Controllers\Auth\MFAChallenge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Str;

class VerifyPinCodeController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'pin_code' => 'required|string',
        ]);

        $user = $request->user();

        if ($user->validatePinCode($request->pin_code)) {
            $request->session()->put('auth.mfa_passed', Date::now()->unix());
            $mfa_verification_token = Str::random(60);
            $request->session()->put('auth.mfa_verification_token', $mfa_verification_token);

            return response()->json(['status' => 'mfa_verified', 'mfa_verification_token' => $mfa_verification_token]);
        }

        return response()->json(['error' => 'Invalid pin code.'], 422);
    }
}
