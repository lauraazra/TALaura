<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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
    public function boot(): void
    {
        // Define Middleware Admin
        Gate::define('admin', function(User $user){
            return $user->role === 'admin';
        });

        // Tombol Paginator Bootstrap Laravel
        Paginator::useBootstrapFive();
    }
}
