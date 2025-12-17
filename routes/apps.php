<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::get('', [AppController::class, 'index'])
    ->name('apps.index');
Route::get('{client}/edit', [AppController::class, 'edit'])
    ->name('apps.edit');
Route::put('{client}', [AppController::class, 'update'])
    ->name('apps.update');
Route::get('create', [AppController::class, 'create'])
    ->name('apps.create');
Route::post('', [AppController::class, 'store'])
    ->name('apps.store');
Route::delete('{client}', [AppController::class, 'destroy'])
    ->name('apps.destroy');
