<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{
    User,
    Classes,
    Task,
    TaskGroup,
    Submission,
    TaskAttachment,
    SubmissionAttachment
};
use App\Policies\{
    UserPolicy,
    ClassPolicy,
    TaskPolicy,
    TaskGroupPolicy,
    SubmissionPolicy,
    AttachmentPolicy
};

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Classes::class => ClassPolicy::class,
        Task::class => TaskPolicy::class,
        TaskGroup::class => TaskGroupPolicy::class,
        Submission::class => SubmissionPolicy::class,
        TaskAttachment::class => AttachmentPolicy::class,
        SubmissionAttachment::class => AttachmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for specific actions
        Gate::define('manage-class', function (User $user) {
            return in_array($user->role, ['admin', 'teacher']);
        });

        Gate::define('submit-task', function (User $user) {
            return $user->role === 'student';
        });

        Gate::define('grade-submission', function (User $user) {
            return in_array($user->role, ['admin', 'teacher']);
        });
    }
}
