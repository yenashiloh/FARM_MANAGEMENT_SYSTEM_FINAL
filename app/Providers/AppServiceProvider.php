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
    public function boot()
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('role', \App\Http\Middleware\RoleAuthenticate::class);
    }
}
