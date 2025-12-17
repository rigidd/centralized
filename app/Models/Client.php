<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    protected $hidden = [
        'secret',
    ];

    protected $casts = [
        'display' => 'boolean',
    ];

    #[Scope]
    public function displayed(Builder $query)
    {
        return $query->where('display', true);
    }
}
