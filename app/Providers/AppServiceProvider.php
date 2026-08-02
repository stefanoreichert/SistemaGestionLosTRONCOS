<?php

namespace App\Providers;

use App\Domain\Table\Entities\Order;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Policies\OrderPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
