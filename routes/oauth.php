<?php

use App\Http\Controllers\OAuth\AccessTokenController;
use App\Http\Controllers\OAuth\AuthorizationController;
use App\Http\Controllers\OAuth\LogoutController;
use Illuminate\Support\Facades\Route;

Route::get('/authorize', [AuthorizationController::class, 'authorize'])
    ->middleware('web')
    ->name('passport.authorizations.authorize');

Route::post('/token', [AccessTokenController::class, 'issueToken'])
    ->withoutMiddleware('web')
    ->name('passport.token');

Route::get('logout', LogoutController::class)
    ->name('oauth.logout');
