<?php

return [
    'v2_enabled' => env('QOOKIEQLOUD_V2_ENABLED', true),
    'app_url' => env('QOOKIEQLOUD_APP_URL', 'https://app.qookieqloud.com'),
    'v2_loader_url' => env('QOOKIEQLOUD_V2_LOADER_URL', 'https://cf-cdn.qookieqloud.com/v2/consentLoader.js'),
    'enabled' => env('QOOKIEQLOUD_ENABLED', true),

    'load_for_authenticated' => env('QOOKIEQLOUD_LOAD_FOR_AUTHENTICATED', false),
];
