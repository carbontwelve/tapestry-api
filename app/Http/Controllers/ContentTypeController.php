<?php

namespace App\Http\Controllers;

use App\Definitions\Classification;
use App\Definitions\ContentType;
use App\Definitions\JsonDefinition;
use App\Definitions\Taxonomy;
use App\JsonRenderer;
use App\Resources\ProjectResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Console\Output\NullOutput;

class ContentTypeController extends BaseController
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

    public function index(Request $request, Response $response, array $args)
    {
        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        /** @var \Tapestry\Modules\ContentTypes\ContentTypeFactory $model */
        $model = $this->project['content_types'];
        $contentTypes = [];

        /** @var \Tapestry\Entities\ContentType $contentType */
        foreach ($model->all() as $contentType) {
            if (!$contentType->isEnabled()) {
                continue;
            }
            $contentType = new ContentType($contentType, $project, $this->container);
            array_push($contentTypes, $contentType->toJsonResponse());
        }

        $jsonResponse = new JsonRenderer($contentTypes);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }

    public function view(ServerRequestInterface $request, Response $response, array $args)
    {
        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        /** @var \Tapestry\Entities\ContentType|null $model */
        if (! $model = $this->project['content_types.' . $args['contentType']]) {
            return $response->withStatus(404);
        }

        /** @var ContentType $contentType */
        $contentType = new ContentType($model, $project, $this->container);
        $contentType = $contentType->apply(function(JsonDefinition $definition){
            $definition->unsetLink('self');
            return $definition;
        });

        $contentType = $contentType
            ->withTaxonomiesRelationship(function(Taxonomy $taxonomy){
                return $taxonomy->withClassificationRelationship();
            })
            ->withFilesRelationship();

        $jsonResponse = new JsonRenderer([$contentType->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }

    public function taxonomies(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->bootProject(new NullOutput());

        /** @var \Tapestry\Entities\ContentType|null $model */
        if (! $model = $this->project['content_types.' . $args['contentType']]) {
            return $response->withStatus(404);
        }

        $contentType = new ContentType($model, $this->container);
        $contentType = $contentType->withTaxonomiesRelationship();

        $data = [];

        foreach ($contentType->getRelationships() as $relationship){
            array_push($data, $relationship->toJsonResponse());
        }

        $jsonResponse = new JsonRenderer($data);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }

    public function taxonomy(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->bootProject(new NullOutput());

        /** @var \Tapestry\Entities\ContentType|null $model */
        if (! $model = $this->project['content_types.' . $args['contentType']]) {
            return $response->withStatus(404);
        }

        $contentType = new ContentType($model, $this->container);
        $contentType = $contentType->withTaxonomiesRelationship();

        /** @var Taxonomy|null $taxonomy */
        if (! $taxonomy = $contentType->getRelationship($args['taxonomy'])) {
            return $response->withStatus(404);
        }

        // This needs to come before the apply, because the related link needs to exist for the classifications
        // to have theirs successfully set.
        $taxonomy = $taxonomy->withClassificationRelationship();

        $taxonomy = $taxonomy->apply(function(JsonDefinition $definition){
            $definition->unsetLink('related');
            return $definition;
        });

        $jsonResponse = new JsonRenderer([$taxonomy->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }

    public function classification(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->bootProject(new NullOutput());

        /** @var \Tapestry\Entities\ContentType|null $model */
        if (! $model = $this->project['content_types.' . $args['contentType']]) {
            return $response->withStatus(404);
        }

        $contentType = new ContentType($model, $this->container);
        $contentType = $contentType->withTaxonomiesRelationship();

        /** @var Taxonomy|null $taxonomy */
        if (! $taxonomy = $contentType->getRelationship($args['taxonomy'])) {
            return $response->withStatus(404);
        }

        $taxonomy = $taxonomy->withClassificationRelationship();

        /** @var Classification $classification */
        if (! $classification = $taxonomy->getRelationship($args['classification'])) {
            return $response->withStatus(404);
        }

        $classification = $classification->apply(function(JsonDefinition $definition){
            $definition->unsetLink('related');
            return $definition;
        });

        $classification = $classification->withFilesRelationship();

        $jsonResponse = new JsonRenderer([$classification->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }
}
