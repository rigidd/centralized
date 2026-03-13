<?php

namespace App\DTOs\Session;

readonly class DestroyOtherBrowserSessionsDTO
{
    public function __construct(
        public int $userId,
        public string $currentSessionId
    ) {}
}
