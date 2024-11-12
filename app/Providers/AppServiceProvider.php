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
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            // Check if the model is an instance of Admin or User
            if ($notifiable instanceof Admin) {
                return 'http://localhost:2000/admin/reset-password/' . $token;
            }
            
            // Default to user URL
            return 'http://localhost:2000/reset-password/' . $token;
        });
    }
}
