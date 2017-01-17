<?php

// Application routes

$app->get('/', 'App\Http\Controllers\ExampleController:index')
    ->setName('home');

$app->get('/content-types', 'App\Http\Controllers\ContentTypeController:index')
    ->setName('content-type.index');

$app->get('/content-types/{contentType}/taxonomy/{taxonomy}', 'App\Http\Controllers\ContentTypeController:taxonomy')
    ->setName('content-type.taxonomy');

$app->get('/fs', 'App\Http\Controllers\FilesystemController:index');