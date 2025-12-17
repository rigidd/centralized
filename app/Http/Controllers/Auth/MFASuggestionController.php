<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MFASuggestionController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Auth/MFASuggestion', [
            'mfa_verification_token' => session('mfa_verification_token'),
        ]);
    }
}
