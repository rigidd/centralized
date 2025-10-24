<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserAdminMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => config('auth.registration'),
    ]);
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('/apps')->middleware(['auth', 'verified', UserAdminMiddleware::class])->group(function () {
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
});

Route::prefix('/users')->middleware(['auth', 'verified', UserAdminMiddleware::class])->group(function () {
    Route::get('', [UserController::class, 'index'])
        ->name('users.index');
    Route::get('{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::put('{user}', [UserController::class, 'update'])
        ->name('users.update');
    Route::get('create', [UserController::class, 'create'])
        ->name('users.create');
    Route::post('', [UserController::class, 'store'])
        ->name('users.store');
    Route::delete('{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');
});

Route::prefix('/groups')->middleware(['auth', 'verified', UserAdminMiddleware::class])->group(function () {
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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('oauth')->group(base_path('routes/oauth.php'));

require __DIR__.'/auth.php';
