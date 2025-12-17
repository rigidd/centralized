<?php

namespace App\Http\Controllers\Auth\MFAChallenge;

use App\Http\Controllers\Controller;
use App\Notifications\User\UserPinCodeNotification;
use Illuminate\Http\Request;

class SendPinCodeController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:mail',
        ]);

        $user = $request->user();

        $pinCode = $user->requestPinCode();
        $user->notify(new UserPinCodeNotification($user, $pinCode, $request->channel));

        return response()->json(['status' => 'pin_code_sent', 'channel' => $request->channel]);
    }
}
