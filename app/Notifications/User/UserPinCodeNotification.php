<?php

namespace App\Notifications\User;

use App\Models\User;
use App\Models\UserPinCode;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPinCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $user,
        private readonly UserPinCode $userPinCode,
        private readonly ?string $channel = 'mail'
    ) {}

    public function via(): array
    {
        return [$this->channel];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Pin Code is ready')
            ->greeting("Hello {$this->user->name}!")
            ->line('You have requested a pin code for two-factor authentication.')
            ->line("Your pin code is: {$this->userPinCode->pin_code}")
            ->line('Please use this code to complete your login process.')
            ->line('This pin code will expire in ' . $this->userPinCode->expires_at->diffForHumans() . '.');
    }
}
