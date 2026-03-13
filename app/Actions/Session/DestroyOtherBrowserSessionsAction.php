<?php

namespace App\Actions\Session;

use App\Models\Session;
use App\DTOs\Session\DestroyOtherBrowserSessionsDTO;

class DestroyOtherBrowserSessionsAction
{
    public function handle(DestroyOtherBrowserSessionsDTO $dto): void
    {
        Session::where('user_id', $dto->userId)
            ->where('id', '!=', $dto->currentSessionId)
            ->delete();
    }
}
