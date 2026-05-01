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
        // ========== Shared Repository Bindings ==========
        // These repositories are used by both Mobile and CMS
        
        $this->app->bind(
            \App\Repositories\Shared\UserCreditRepositoryInterface::class,
            \App\Repositories\Shared\UserCreditRepository::class
        );

        $this->app->bind(
            \App\Repositories\Shared\DailyLoginClaimRepositoryInterface::class,
            \App\Repositories\Shared\DailyLoginClaimRepository::class
        );

        $this->app->bind(
            \App\Repositories\Shared\UserProgressRepositoryInterface::class,
            \App\Repositories\Shared\UserProgressRepository::class
        );

        $this->app->bind(
            \App\Repositories\Shared\JlptRepositoryInterface::class,
            \App\Repositories\Shared\JlptRepository::class
        );

        $this->app->bind(
            \App\Repositories\Shared\UserNoteRepositoryInterface::class,
            \App\Repositories\Shared\UserNoteRepository::class
        );

        $this->app->bind(
            \App\Repositories\Shared\CertificateRepositoryInterface::class,
            \App\Repositories\Shared\CertificateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Shared\AdWatchRepositoryInterface::class,
            \App\Repositories\Shared\AdWatchRepository::class
        );

        $this->app->bind(
            \App\Repositories\Shared\PartnershipRepositoryInterface::class,
            \App\Repositories\Shared\PartnershipRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
