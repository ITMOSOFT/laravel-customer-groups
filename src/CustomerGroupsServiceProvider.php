<?php

namespace ItmoSoft\CustomerGroups;

use Illuminate\Support\ServiceProvider;

class CustomerGroupsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/itmosoft-customer-groups.php',
            'itmosoft-customer-groups'
        );

        $this->app->singleton(CustomerGroupManager::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/itmosoft-customer-groups.php' => config_path('itmosoft-customer-groups.php'),
        ], 'itmosoft-customer-groups-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
