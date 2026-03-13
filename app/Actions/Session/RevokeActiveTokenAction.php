<?php

namespace App\Actions\Session;

use App\Models\User;
use App\DTOs\Session\RevokeActiveTokenDTO;

class RevokeActiveTokenAction
{
    public function handle(RevokeActiveTokenDTO $dto): void
    {
        $user = User::findOrFail($dto->userId);
        
        $token = $user->tokens()
            ->where('id', $dto->tokenId)
            ->where('revoked', false)
            ->firstOrFail();
            
        $token->revoke();
    }
}
