<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientRedirectAlias extends Model
{
    protected $fillable = [
        'client_id',
        'url',
        'alias',
        'icon',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
