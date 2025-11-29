<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $request = request();
        if (!$request->header('Authorization') && $request->header('X-Auth-Token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->header('X-Auth-Token'));
        }
    }
}