<?php

use App\Http\Controllers\API\MeController;
use Illuminate\Support\Facades\Route;

Route::get('/user', MeController::class)
    ->middleware('auth:api')
    ->name('user.get');

Route::get('/userinfo', MeController::class)
    ->middleware('auth:api')
    ->name('openid.userinfo');
