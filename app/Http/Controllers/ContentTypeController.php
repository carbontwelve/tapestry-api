<?php

namespace App\Http\Controllers;

use App\Definitions\ContentType;
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
}
