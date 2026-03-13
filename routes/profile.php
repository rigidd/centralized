<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\MFAStrictVerificationMiddleware;
use LaravelWebauthn\Http\Controllers\WebauthnKeyController;

Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::delete('/sessions', [ProfileController::class, 'destroySessions'])
    ->middleware(MFAStrictVerificationMiddleware::class)
    ->name('profile.sessions.destroy');
Route::delete('/tokens/{token}', [ProfileController::class, 'destroyToken'])
    ->middleware(MFAStrictVerificationMiddleware::class)
    ->name('profile.tokens.destroy');

Route::prefix('webauthn')->group(function () {
    Route::post('keys/options', [WebauthnKeyController::class, 'create'])
        ->name('profile.webauthn.store.options');
    Route::post('keys', [WebauthnKeyController::class, 'store'])
        ->middleware(MFAStrictVerificationMiddleware::class)
        ->name('profile.webauthn.store');
    Route::delete('keys/{id}', [WebauthnKeyController::class, 'destroy'])
        ->middleware(MFAStrictVerificationMiddleware::class)
        ->name('profile.webauthn.destroy');
});