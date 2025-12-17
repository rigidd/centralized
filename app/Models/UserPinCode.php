<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPinCode extends Model
{
    protected $fillable = [
        'user_id',
        'pin_code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function generateForUser(User $user, int $validityMinutes = 10): self
    {
        $pinCode = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'user_id' => $user->id,
            'pin_code' => $pinCode,
            'expires_at' => now()->addMinutes($validityMinutes),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
