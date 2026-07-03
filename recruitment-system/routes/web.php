<?php

use App\Http\Controllers\Careers\JobController;
use App\Http\Controllers\Careers\ApplicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Careers Website Routes
Route::prefix('careers')->name('careers.')->group(function () {
    // Home / Landing Page
    Route::get('/', function () {
        $featuredJobs = \App\Models\JobPosting::published()
            ->featured()
            ->with(['department', 'location'])
            ->limit(6)
            ->get();

        $departments = \App\Models\Department::active()
            ->whereHas('jobPostings', fn($q) => $q->published())
            ->withCount(['jobPostings' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return view('careers.index', compact('featuredJobs', 'departments'));
    })->name('home');

    // Job Listings
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');

    // Applications
    Route::get('/apply/{slug}', [ApplicationController::class, 'create'])->name('apply');
    Route::post('/apply/{slug}', [ApplicationController::class, 'store'])->name('apply.store');
    Route::get('/thank-you/{application}', [ApplicationController::class, 'thankYou'])->name('thank-you');

    // Company Pages
    Route::get('/about', function () {
        return view('careers.company.about');
    })->name('about');

    Route::get('/culture', function () {
        return view('careers.company.culture');
    })->name('culture');

    Route::get('/benefits', function () {
        return view('careers.company.benefits');
    })->name('benefits');
});

// Admin Panel Routes (Filament)
// Route::get('/admin', function () {
//     return redirect('/admin/login');
// });

// Default Route - Redirect to careers
Route::get('/', function () {
    return redirect()->route('careers.home');
});
