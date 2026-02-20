<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('files', fn () => new \Illuminate\Filesystem\Filesystem);
        $this->app->singleton('cache', fn ($app) => new \Illuminate\Cache\CacheManager($app));
        $this->app->singleton('migration.repository', fn ($app) => new \Illuminate\Database\Migrations\DatabaseMigrationRepository($app['db'], 'migrations'));
        $this->app->singleton('migrator', fn ($app) => new \Illuminate\Database\Migrations\Migrator($app['migration.repository'], $app['db'], $app['files'], $app['events']));
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.tailwind');
    }
}
