<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        // RBAC: Hanya role approver atau admin yang bisa manage approval
        Gate::define('manage-approval', function (User $user) {
            return in_array($user->role, ['approver', 'admin']);
        });
    }
}
