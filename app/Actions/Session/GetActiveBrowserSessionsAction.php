<?php

namespace App\Actions\Session;

use App\Models\User;
use App\Models\Session;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GetActiveBrowserSessionsAction
{
    public function handle(User $user, string $currentSessionId): Collection
    {
        return Session::where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $agent = new Agent();
                $agent->setUserAgent($session->user_agent);

                return (object) [
                    'agent' => (object) [
                        'is_desktop' => $agent->isDesktop(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                    ],
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === $currentSessionId,
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });
    }
}
