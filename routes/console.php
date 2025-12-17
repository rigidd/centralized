<?php

use App\Jobs\PurgeExpiredUserPinCodesJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new PurgeExpiredUserPinCodesJob)->everySixHours();
