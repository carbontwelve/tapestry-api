<?php

namespace App\Http\Controllers;

use App\Definitions\File;
use App\Definitions\JsonDefinition;
use App\Definitions\Path;
use App\JsonRenderer;
use App\Resources\ProjectResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Symfony\Component\Console\Output\NullOutput;

class FilesystemController extends BaseController
{
    /**
     * @var ProjectResource
     */
    private $projectResource;

    /**
     * ProjectController constructor.
     * @param ProjectResource $projectResource
     */
    public function __construct(ProjectResource $projectResource)
    {
        $this->projectResource = $projectResource;
    }

    public function file(ServerRequestInterface $request, Response $response, array $args){

        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        /** @var \Tapestry\Entities\File $tapestryFile */
        if (! $tapestryFile = $this->project['files.' . $args['id']]) {
            return $response->withStatus(404);
        }

        /** @var File $file */
        $file = new File($tapestryFile, $project, $this->container);
        $file = $file->withDirectoryRelationship();

        $file = $file->apply(function(JsonDefinition $definition){
            $definition->unsetLink('self');
            return $definition;
        });

        $jsonResponse = new JsonRenderer(['data' => $file->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }

    public function path(ServerRequestInterface $request, Response $response, array $args){

        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        $path = (isset($args['id']) ? base64_decode($args['id']) : "");

        if (realpath($this->project->sourceDirectory . DIRECTORY_SEPARATOR . $path) === false){
                return $response->withStatus(404);
        }

        $path = new Path($path, $this->container);
        $path = $path->withPathRelationship()
            ->withProjectFileRelationship();
        $jsonResponse = new JsonRenderer([$path->toJsonResponse()]);
        $jsonResponse->inheritLinks();
        return $jsonResponse->render($response);
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
