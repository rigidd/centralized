<?php

use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::get('', [GroupController::class, 'index'])
    ->name('groups.index');
Route::get('{group}/edit', [GroupController::class, 'edit'])
    ->name('groups.edit');
Route::put('{group}', [GroupController::class, 'update'])
    ->name('groups.update');
Route::get('create', [GroupController::class, 'create'])
    ->name('groups.create');
Route::post('', [GroupController::class, 'store'])
    ->name('groups.store');
Route::delete('{group}', [GroupController::class, 'destroy'])
    ->name('groups.destroy');