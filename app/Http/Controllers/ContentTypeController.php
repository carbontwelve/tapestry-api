<?php

namespace App\Http\Controllers;

use App\Definitions\ContentType;
use App\Definitions\JsonDefinition;
use App\Definitions\Taxonomy;
use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Output\NullOutput;

class ContentTypeController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->bootProject(new NullOutput());

        /** @var \Tapestry\Modules\ContentTypes\ContentTypeFactory $model */
        $model = $this->project['content_types'];
        $contentTypes = [];

        /** @var \Tapestry\Entities\ContentType $contentType */
        foreach ($model->all() as $contentType) {
            if (!$contentType->isEnabled()) {
                continue;
            }
            $contentType = new ContentType($contentType, $this->container);
            array_push($contentTypes, $contentType->toJsonResponse());
        }

        $jsonResponse = new JsonRenderer($contentTypes);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }

    public function view(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->bootProject(new NullOutput());

        /** @var \Tapestry\Entities\ContentType|null $model */
        if (! $model = $this->project['content_types.' . $args['contentType']]) {
            return $response->withStatus(404);
        }

        $contentType = new ContentType($model, $this->container);
        $contentType = $contentType->apply(function(JsonDefinition $definition){
            $definition->unsetLink('self');
            return $definition;
        });

        $jsonResponse = new JsonRenderer([$contentType->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
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

        /** @var Taxonomy|null $taxonomy */
        if (! $taxonomy = $contentType->getRelationship($args['taxonomy'])) {
            return $response->withStatus(404);
        }

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

        /** @var Taxonomy|null $taxonomy */
        if (! $taxonomy = $contentType->getRelationship($args['taxonomy'])) {
            return $response->withStatus(404);
        }

        if (! $classification = $taxonomy->getRelationship($args['classification'])) {
            return $response->withStatus(404);
        }

        $classification = $classification->apply(function(JsonDefinition $definition){
            $definition->unsetLink('related');
            return $definition;
        });

        $jsonResponse = new JsonRenderer([$classification->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }
}
