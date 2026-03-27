<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'view'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('service')->name('service.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/status', [ServiceController::class, 'status'])->name('status');
    Route::post('/start', [ServiceController::class, 'start'])->name('start');
    Route::post('/stop', [ServiceController::class, 'stop'])->name('stop');
    Route::post('/restart', [ServiceController::class, 'restart'])->name('restart');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/track/add', [TrackController::class, 'showAddForm'])->name('track.show-add-form');
    Route::get('/track/{trackedAccount}', [TrackController::class, 'showEditForm'])->name('track.show-edit-form');
    Route::post('/track', [TrackController::class, 'save'])->name('track.add');
    Route::patch('/track/{trackedAccount}', [TrackController::class, 'save'])->name('track.update');
});

Route::post('/telegram/webhook/{token}', [TelegramController::class, 'webhook'])->name('telegram.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/auth.php';
