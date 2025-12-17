<?php

namespace App\Notifications\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class UserCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $user,
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $resetToken = Password::broker('users_activation')->createToken($this->user);

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->greeting('Welcome to ' . config('app.name') . '!')
            ->line('Your account has been created.')
            ->line('Please activate your account to get started.')
            ->action('Activate Account', route('activate-account', [
                'token' => $resetToken,
                'email' => $this->user->email,
            ]))
            ->line('This activation link will expire in 7 days.');
    }
}
