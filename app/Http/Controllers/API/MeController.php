<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        return response()->json($user->load('groups'));
    }
}
