<?php

use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\ApplicationController;
use App\Http\Controllers\Portal\InterviewController;
use App\Http\Controllers\Portal\DocumentController;
use App\Http\Controllers\Portal\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Candidate Portal Routes
|--------------------------------------------------------------------------
*/

Route::prefix('portal')->name('candidate.')->group(function () {

    // Public Guest Routes
    Route::middleware('guest:candidate')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);

        Route::get('/set-password', [AuthController::class, 'showSetPassword'])->name('set-password');
        Route::post('/set-password', [AuthController::class, 'setPassword'])->name('set-password.store');

        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('forgot-password.email');
        
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    });

    // Protected Auth Routes
    Route::middleware('auth:candidate')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Application Tracking
        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
        Route::post('/applications/{application}/offer/accept', [ApplicationController::class, 'acceptOffer'])->name('applications.offer.accept');
        Route::post('/applications/{application}/offer/reject', [ApplicationController::class, 'rejectOffer'])->name('applications.offer.reject');

        // Interviews
        Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');

        // Documents
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
