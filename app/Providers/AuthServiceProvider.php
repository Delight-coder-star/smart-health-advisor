<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Assessment;
use App\Models\Alert;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Assessment::class => \App\Policies\AssessmentPolicy::class,
        Alert::class => \App\Policies\AlertPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('advisor', function (User $user) {
            return $user->isAdvisor() || $user->isAdmin();
        });

        Gate::define('student', function (User $user) {
            return $user->isStudent();
        });

        Gate::define('view-assessment', function (User $user, Assessment $assessment) {
            return $user->id === $assessment->user_id 
                || $user->isAdvisor() 
                || $user->isAdmin();
        });
    }
}