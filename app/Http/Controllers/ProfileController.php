<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Actions\Session\DestroyOtherBrowserSessionsAction;
use App\Actions\Session\GetActiveBrowserSessionsAction;
use App\Actions\Session\GetActiveConnectedAppsAction;
use App\Actions\Session\RevokeActiveTokenAction;
use App\DTOs\Session\DestroyOtherBrowserSessionsDTO;
use App\DTOs\Session\RevokeActiveTokenDTO;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(
        Request $request,
        GetActiveBrowserSessionsAction $getSessionsAction,
        GetActiveConnectedAppsAction $getAppsAction
    ): Response {
        $user = $request->user();

        return Inertia::render('Profile/Edit', [
            'webauthnKeys' => $user->webauthnKeys,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'sessions' => $getSessionsAction->handle($user, $request->session()->getId()),
            'connectedApps' => $getAppsAction->handle($user),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Destroy other active browser sessions safely.
     */
    public function destroySessions(
        Request $request,
        DestroyOtherBrowserSessionsAction $action
    ): RedirectResponse {
        $dto = new DestroyOtherBrowserSessionsDTO(
            userId: $request->user()->id,
            currentSessionId: $request->session()->getId()
        );

        $action->handle($dto);

        return Redirect::route('profile.edit')->with('status', 'browser-sessions-terminated');
    }

    /**
     * Revoke an active third-party application token.
     */
    public function destroyToken(
        Request $request,
        string $tokenId,
        RevokeActiveTokenAction $action
    ): RedirectResponse {
        
        $dto = new RevokeActiveTokenDTO(
            userId: $request->user()->id,
            tokenId: $tokenId
        );

        $action->handle($dto);

        return Redirect::route('profile.edit')->with('status', 'app-authorization-revoked');
    }
}
