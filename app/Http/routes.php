<?php

// Application routes

$app->get('/', 'App\Http\Controllers\ExampleController:index')
    ->setName('home');

$app->get('/content-types', 'App\Http\Controllers\ContentTypeController:index')
    ->setName('content-type.index');

$app->get('/content-types/{contentType}', 'App\Http\Controllers\ContentTypeController:view')
    ->setName('content-type.view');

$app->get('/content-types/{contentType}/taxonomies', 'App\Http\Controllers\ContentTypeController:taxonomies')
    ->setName('content-type.taxonomies');

$app->get('/content-types/{contentType}/taxonomy/{taxonomy}', 'App\Http\Controllers\ContentTypeController:taxonomy')
    ->setName('content-type.taxonomy');

$app->get('/content-types/{contentType}/taxonomy/{taxonomy}/{classification}', 'App\Http\Controllers\ContentTypeController:classification')
    ->setName('content-type.taxonomy.classification');

//
// File System
//
$app->get('/filesystem/file/{id}', 'App\Http\Controllers\FilesystemController:file')
    ->setName('filesystem.file');

$app->get('/filesystem/directory[/{id}]', 'App\Http\Controllers\FilesystemController:directory')
    ->setName('filesystem.directory');

$app->get('/fs', 'App\Http\Controllers\FilesystemController:index');

