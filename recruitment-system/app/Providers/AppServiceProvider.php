<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\ApplicationSubmitted::class,
            \App\Listeners\SendCandidatePortalInvite::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\ApplicationStatusChanged::class,
            \App\Listeners\SendApplicationStatusEmail::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \App\Models\Candidate::class,
            \App\Policies\CandidatePolicy::class
        );
    }
}
