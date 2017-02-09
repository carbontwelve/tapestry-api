<?php

namespace App\Http\Controllers;

use App\Definitions\File;
use App\Definitions\JsonDefinition;
use App\Definitions\Path;
use App\JsonRenderer;
use App\Resources\ProjectResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Yaml\Yaml;

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

    public function path(Request $request, Response $response, array $args)
    {

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

    public function view(Request $request, Response $response, array $args)
    {
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
        $jsonResponse->inheritLinks();
        return $jsonResponse->render($response);
    }

    public function create(Request $request, Response $response, array $args)
    {
        // ...
    }

    public function update(Request $request, Response $response, array $args)
    {
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
        $file = $file->merge($request->getParsedBodyParam('file'));

        if ($file->save() === false) {
            return $this->abort($response, 'File could not be saved.');
        }

        $jsonResponse = new JsonRenderer(['data' => $file->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        // ...
    }
}
