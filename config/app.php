<?php

return [
    'name' => env('APP_NAME', 'Frizerski Salon'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'angular_url' => env('ANGULAR_URL'),
    'timezone' => env('APP_TIMEZONE', 'Europe/Belgrade'),
    'locale' => env('APP_LOCALE', 'sr'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'sr_RS'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
];
