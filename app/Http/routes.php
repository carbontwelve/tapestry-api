<?php

// https://www.slimframework.com/docs/cookbook/enable-cors.html
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->get('/', 'App\Http\Controllers\ExampleController:index')
    ->setName('home');

//
// Handshake and Authentication Routes
//

$app->get('/handshake', 'App\Http\Controllers\AuthenticationController:handshake')
    ->setName('handshake');

$app->post('/authenticate', 'App\Http\Controllers\AuthenticationController:authenticate')
    ->setName('authenticate');

//
// Project Routes
//

$app->get('/project/content-types', 'App\Http\Controllers\ContentTypeController:index')
    ->setName('content-type.index');

$app->get('/project/content-types/{contentType}', 'App\Http\Controllers\ContentTypeController:view')
    ->setName('content-type.view');

$app->get('/project/content-types/{contentType}/taxonomies', 'App\Http\Controllers\ContentTypeController:taxonomies')
    ->setName('content-type.taxonomies');

$app->get('/project/content-types/{contentType}/taxonomy/{taxonomy}', 'App\Http\Controllers\ContentTypeController:taxonomy')
    ->setName('content-type.taxonomy');

$app->get('/project/content-types/{contentType}/taxonomy/{taxonomy}/{classification}', 'App\Http\Controllers\ContentTypeController:classification')
    ->setName('content-type.taxonomy.classification');

$app->get('/project/file/{id}', 'App\Http\Controllers\FilesystemController:file')
    ->setName('project.file');

//
// File System
//

$app->get('/filesystem/directory[/{id}]', 'App\Http\Controllers\FilesystemController:directory')
    ->setName('filesystem.directory');

$app->get('/filesystem[/{id}]', 'App\Http\Controllers\FilesystemController:path')
    ->setName('filesystem.path');
