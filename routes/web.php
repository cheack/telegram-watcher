<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'view'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/track/add', [TrackController::class, 'showAddForm'])->name('track.show-add-form');
    Route::get('/track/{trackedAccount}', [TrackController::class, 'showEditForm'])->name('track.show-edit-form');
    Route::post('/track', [TrackController::class, 'save'])->name('track.add');
    Route::patch('/track/{trackedAccount}', [TrackController::class, 'save'])->name('track.update');
});

Route::post('/telegram/webhook/{token}', [TelegramController::class, 'webhook'])->name('telegram.webhook');

require __DIR__.'/auth.php';
