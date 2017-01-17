<?php

return [
    'debug' => true,
    'whoops.editor' => 'sublime', // Support click to open editor

    'displayErrorDetails' => true, // set to false in production
    'determineRouteBeforeAppMiddleware' => false,
    'outputBuffering' => 'append',
    'responseChunkSize' => 4096,
    'httpVersion' => '1.1',

    // Monolog settings
    'logger' => [
        'name' => 'slim-app',
        'path' => realpath(__DIR__ . '/../storage/framework/logs/') . DIRECTORY_SEPARATOR . 'app-' . date('dmY') . '.log',
    ],

    // Services
    'services' => [
        \App\Providers\SessionProvider::class,
        \App\Providers\LoggerProvider::class,
        \App\Providers\TapestryCoreProvider::class,
        \App\Providers\ControllerProvider::class,
    ],
];
