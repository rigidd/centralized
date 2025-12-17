<?php

namespace App\Jobs;

use App\Models\UserPinCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurgeExpiredUserPinCodesJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        UserPinCode::where('expires_at', '<', now())->delete();
    }
}
