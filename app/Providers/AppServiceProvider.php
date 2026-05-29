<?php

namespace App\Providers;

use App\Models\Video;
use App\Policies\VideoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Video::class, VideoPolicy::class);
        
        // Admin gate
        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });
    }
}
