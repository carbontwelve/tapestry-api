<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExampleController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $manager = $this->tapestry[\Tapestry\Content\Manager::class];

        //dd($tapestry->getPath('dist'));

        /** @var \Tapestry\Content\Configuration $configuration */
        $configuration = $this->tapestry[\Tapestry\Content\Configuration::class];
        $contentTypes = [];

        foreach($configuration->get('content_types') as $contentTypeName => $contentTypeConfig) {
            $tmp = new \stdClass();
            $tmp->type = "content-type";
            $tmp->attributes = [
                'name' => $contentTypeName,
                'taxonomies' => $contentTypeConfig['taxonomies']
            ];
            array_push($contentTypes, $tmp);
        }

        $jsonResponse = new JsonRenderer($contentTypes);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()
        ]);
        return $jsonResponse->render($response);
    }
}
