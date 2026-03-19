<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\View\Composers\UnviewedApplicationsComposer;
use App\View\Composers\UnreadMessagesComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;


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
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('admin.components.sidebar', UnviewedApplicationsComposer::class);
        View::composer('admin.components.sidebar', UnreadMessagesComposer::class);

    }
}
