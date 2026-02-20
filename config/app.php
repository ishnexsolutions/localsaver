<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'LocalSaver'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Kolkata',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
    ],
    'providers' => array_merge(
        require __DIR__.'/../bootstrap/providers.php',
        ServiceProvider::defaultProviders()->toArray()
    ),
    'aliases' => Facade::defaultAliases()->merge([])->toArray(),
];
