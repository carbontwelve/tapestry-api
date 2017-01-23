<?php

namespace App\Http\Controllers;

use App\Definitions\Directory;
use App\Definitions\File;
use App\Definitions\JsonDefinition;
use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Output\NullOutput;

class FilesystemController extends BaseController
{
    public function file(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $this->bootProject(new NullOutput());

        /** @var \Tapestry\Entities\File $tapestryFile */
        if (! $tapestryFile = $this->project['files.' . $args['id']]) {
            return $response->withStatus(404);
        }

        $file = new File($tapestryFile, $this->container);
        $file = $file->withDirectoryRelationship();

        $file = $file->apply(function(JsonDefinition $definition){
            $definition->unsetLink('self');
            return $definition;
        });

        $jsonResponse = new JsonRenderer([$file->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }

    public function directory(ServerRequestInterface $request, ResponseInterface $response, array $args){

        return 'yo';

        //$this->bootProject(new NullOutput());

        //$path = "";

        //if (isset($args['id'])) {
        //    $path = base64_decode($args['id']);
        //}

        //$realPath = realpath($this->project->sourceDirectory . DIRECTORY_SEPARATOR . $path);

        //if (!$realPath){
        //    return $response->withStatus(404);
        //}

        //$directory = new Directory($path, $this->container);
        //$directory = $directory
        //    ->withFilesRelationship()
        //    ->withDirectoriesRelationship();

        //$jsonResponse = new JsonRenderer([$directory->toJsonResponse()]);
        //$jsonResponse->setLinks([
        //    'self' => (string)$request->getUri()->getPath(),
        //]);
        //return $jsonResponse->render($response);
    }

    public function view(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }
}
