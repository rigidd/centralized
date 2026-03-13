<?php

namespace App\DTOs\Session;

readonly class RevokeActiveTokenDTO
{
    public function __construct(
        public int $userId,
        public string $tokenId
    ) {}
}
