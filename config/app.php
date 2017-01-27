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

    // Database settings
    'doctrine' => [
        'meta' => [
            'entity_path' => [
                'app/Entity'
            ],
            'auto_generate_proxies' => true,
            'proxy_dir' =>  __DIR__.'/../storage/cache/proxies',
            'cache' => null,
        ],
        'connection' => [
            'driver'   => 'pdo_sqlite',
            'path'     => __DIR__.'/../storage/database.sqlite'
        ]
    ],

    // Services
    'services' => [
        \App\Providers\SessionProvider::class,
        \App\Providers\LoggerProvider::class,
        \App\Providers\TapestryCoreProvider::class,
        \App\Providers\DoctrineProvider::class,
        \App\Providers\ControllerProvider::class,
    ],
];
