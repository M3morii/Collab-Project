<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\{
    AuthService,
    ClassService,
    TaskService,
    TaskGroupService,
    SubmissionService,
    FileService,
    NotificationService
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Auth Service
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });

        // Register Class Service
        $this->app->singleton(ClassService::class, function ($app) {
            return new ClassService();
        });

        // Register Task Service
        $this->app->singleton(TaskService::class, function ($app) {
            return new TaskService(
                $app->make(FileService::class)
            );
        });

        // Register Task Group Service
        $this->app->singleton(TaskGroupService::class, function ($app) {
            return new TaskGroupService();
        });

        // Register Submission Service
        $this->app->singleton(SubmissionService::class, function ($app) {
            return new SubmissionService(
                $app->make(FileService::class)
            );
        });

        // Register File Service
        $this->app->singleton(FileService::class, function ($app) {
            return new FileService();
        });

        // Register Notification Service
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
