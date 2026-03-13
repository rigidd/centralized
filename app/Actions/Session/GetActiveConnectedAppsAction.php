<?php

namespace App\Actions\Session;

use App\Models\User;
use Illuminate\Support\Collection;

class GetActiveConnectedAppsAction
{
    public function handle(User $user): Collection
    {
        return $user->tokens()
            ->where('revoked', false)
            ->with('client')
            ->get()
            ->map(function ($token) {
                return (object) [
                    'id' => $token->id,
                    'client_name' => $token->client->name,
                    'client_picture' => $token->client->picture,
                    'created_at' => $token->created_at->diffForHumans(),
                ];
            });
    }
}
