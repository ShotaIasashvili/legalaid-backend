<?php

return [
    'name'            => env('APP_NAME', 'Legal Aid Georgia'),
    'env'             => env('APP_ENV', 'production'),
    'debug'           => (bool) env('APP_DEBUG', false),
    'url'             => env('APP_URL', 'http://localhost'),
    'frontend_url'    => env('FRONTEND_URL', env('APP_URL', 'http://localhost')),
    'frontend_prod_url' => env('FRONTEND_PROD_URL', env('APP_URL', 'http://localhost')),
    'legacy_frontend_public_path' => env('LEGACY_FRONTEND_PUBLIC_PATH', base_path('../legalaid2/public')),
    'timezone'        => 'Asia/Tbilisi',
    'locale'          => env('APP_LOCALE', 'ka'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale'    => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher'          => 'AES-256-CBC',
    'key'             => env('APP_KEY'),
    'previous_keys'   => array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store'  => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
    ],

    'aliases' => Illuminate\Support\Facades\Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),
];
