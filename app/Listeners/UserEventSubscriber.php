<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Client;
use App\Models\UserEvent;
use Illuminate\Events\Dispatcher;
use Illuminate\Auth\Events\Login;
use Laravel\Passport\Events\AccessTokenCreated;

class UserEventSubscriber
{
    public function handleLogin($event): void
    {
        if ($event->user) {
            $event->user->events()->create([
                'event_type' => 'login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    public function handleTokenCreated($event): void
    {
        $client = Client::find($event->clientId);
        if ($client) {
            UserEvent::create([
                'user_id' => $event->userId,
                'event_type' => 'app_accessed',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'details' => ['client_name' => $client->name, 'client_id' => $client->id],
            ]);
        }
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            Login::class,
            [UserEventSubscriber::class, 'handleLogin']
        );

        $events->listen(
            AccessTokenCreated::class,
            [UserEventSubscriber::class, 'handleTokenCreated']
        );
    }
}
