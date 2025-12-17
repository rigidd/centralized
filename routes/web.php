<?php

use App\Http\Controllers\DashboardController;
use App\Http\Middleware\MFACheckMiddleware;
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

Route::middleware(['auth', 'verified', MFACheckMiddleware::class])->group(function() {

    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::prefix('/profile')->group(base_path('routes/profile.php'));

    Route::middleware([UserAdminMiddleware::class])->group(function() {

        Route::prefix('/apps')->group(base_path('routes/apps.php'));
        Route::prefix('/users')->group(base_path('routes/users.php'));
        Route::prefix('/groups')->group(base_path('routes/groups.php'));

    });
});


Route::prefix('oauth')->group(base_path('routes/oauth.php'));

Route::prefix('auth')->group(base_path('routes/auth.php'));
