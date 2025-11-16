<?php

use Laravel\Sanctum\Sanctum;

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1,::1')),

    'guard' => ['web'],

    'expiration' => env('SANCTUM_EXPIRATION'),

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
    'authenticate_session' => 'Laravel\\Sanctum\\Http\\Middleware\\AuthenticateSession',
    'encrypt_cookies' => 'Illuminate\\Cookie\\Middleware\\EncryptCookies',
    'validate_csrf_token' => 'Illuminate\\Foundation\\Http\\Middleware\\ValidateCsrfToken',
    ],

    'route_middlewares' => [
    'guard.abilities' => 'Laravel\\Sanctum\\Http\\Middleware\\CheckAbilities',
    'guard.ability' => 'Laravel\\Sanctum\\Http\\Middleware\\CheckForAnyAbility',
    ],

    'providers' => [
        'users' => [
            'model' => App\Models\User::class,
        ],
    ],
];
