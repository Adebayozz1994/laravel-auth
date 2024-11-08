<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
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
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'http://localhost:2000/reset-password/' . $token;
        });

          // URL for Admin password reset
    ResetPassword::createUrlUsing(function (Admin $admin, string $token) {
        return 'http://localhost:2000/admin/reset-password/' . $token;
    });
    }
}
