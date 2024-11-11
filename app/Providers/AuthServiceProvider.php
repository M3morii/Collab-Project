<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Models\Attachment;
use App\Policies\AttachmentPolicy;
use App\Models\Classes;
use App\Policies\ClassPolicy;
use App\Models\Group;
use App\Policies\GroupPolicy;
use App\Models\Submission;
use App\Policies\SubmissionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Classes::class => ClassPolicy::class,
        Group::class => GroupPolicy::class,
        Task::class => TaskPolicy::class,
        Submission::class => SubmissionPolicy::class,
        Attachment::class => AttachmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define role middleware
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('teacher', function ($user) {
            return $user->role === 'teacher';
        });

        Gate::define('student', function ($user) {
            return $user->role === 'student';
        });
    }
}
