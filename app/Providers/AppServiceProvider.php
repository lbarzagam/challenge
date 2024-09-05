<?php

namespace App\Providers;

use App\Services\ProjectService;
use App\Services\TaskService;
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
        $this->app->singleton(TaskService::class, function ($app) {
            return new TaskService();
        });

        $this->app->singleton(ProjectService::class, function ($app) {
            return new ProjectService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
