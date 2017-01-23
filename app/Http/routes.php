<?php

$app->get('/', 'App\Http\Controllers\ExampleController:index')
    ->setName('home');

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

