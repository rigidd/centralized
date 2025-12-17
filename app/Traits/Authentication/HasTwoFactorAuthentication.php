<?php

namespace App\Traits\Authentication;

use App\Models\UserPinCode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaravelWebauthn\Models\WebauthnKey;

trait HasTwoFactorAuthentication
{
    public function mfaEnabled(): bool
    {
        return (
            $this->webauthnKeys()->exists() ||
            config('auth.require_mfa')
        );
    }

    public function requestPinCode($validityMinutes = 10): UserPinCode
    {
        return UserPinCode::generateForUser($this, $validityMinutes);
    }

    public function validatePinCode(string $pinCode): bool
    {
        $userPinCode = $this->pinCodes()
            ->where('pin_code', $pinCode)
            ->latest()
            ->first();

        if (! $userPinCode || $userPinCode->isExpired()) {
            return false;
        }

        $userPinCode->delete();

        return true;
    }

    public function webauthnKeys()
    {
        return $this->hasMany(WebauthnKey::class);
    }

    public function hasWebauthnEnabled(): bool
    {
        return $this->webauthnKeys()->exists();
    }

    public function pinCodes(): HasMany
    {
        return $this->hasMany(UserPinCode::class);
    }
}
