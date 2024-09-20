<?php

namespace App\Providers;

use App\Models\Auth;
use App\Models\Task;
use App\Policies\AuthPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
    protected $policies = [
        Task::class => TaskPolicy::class,
        Auth::class => AuthPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
