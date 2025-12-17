<?php

namespace App\Http\Controllers\Auth\MFAChallenge;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LaravelWebauthn\Facades\Webauthn;

class GetWebAuthnPublicKeyController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $publicKey = Webauthn::prepareAssertion($user);

        return response()->json([
            'publicKey' => $publicKey,
        ]);
    }
}
