<?php

use App\Jobs\PruneOldUserEventsJob;
use App\Jobs\PurgeExpiredUserPinCodesJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new PurgeExpiredUserPinCodesJob)->everySixHours();
Schedule::job(new PruneOldUserEventsJob)->daily();
