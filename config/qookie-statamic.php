<?php

return [
    'enabled' => env('QOOKIEQLOUD_ENABLED', true),

    'loader_url' => env('QOOKIEQLOUD_LOADER_URL', 'https://js.qookieqloud.com/consentLoader.js'),

    'app_url' => env('QOOKIEQLOUD_APP_URL', 'https://app.qookieqloud.com'),

    'load_for_authenticated' => env('QOOKIEQLOUD_LOAD_FOR_AUTHENTICATED', false),
];
