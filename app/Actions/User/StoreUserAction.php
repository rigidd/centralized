<?php

namespace App\Actions\User;

use App\Models\User;
use App\Notifications\User\UserCreatedNotification;
use Str;

class StoreUserAction
{
    public function handle(array $data): User
    {
        $data['password'] = bcrypt(Str::random(16));

        $user = User::create($data);

        $user->notify(new UserCreatedNotification($user));
        return $user;
    }
}