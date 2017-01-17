<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tapestry\Content\Configuration;

class ContentTypeController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        /** @var Configuration $config */
        $config = $this->tapestry[Configuration::class];

        $contentTypes = [];
        foreach($config->get('content_types') as $contentTypeName => $contentTypeAttr) {
            if ($contentTypeAttr['enabled'] === false) {
                continue;
            }

            $tmp = [
                "type" => "contentType",
                "id" => $contentTypeName,
                "attributes" => $contentTypeAttr,
                "relationships" => []
            ];

            if (count($contentTypeAttr['taxonomies']) > 0) {
                foreach ($contentTypeAttr['taxonomies'] as $taxonomy) {
                    $tmp['relationships'][$taxonomy] = [
                        "links" => [
                            "related" => $this->container->get('router')->pathFor('content-type.taxonomy', [
                                'contentType' => $contentTypeName,
                                'taxonomy' => $taxonomy
                            ])
                        ],
                        "data" => [] // @todo implement
                    ];
                }
            }

            array_push($contentTypes, $tmp);
        }

        $jsonResponse = new JsonRenderer($contentTypes);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()
        ]);
        return $jsonResponse->render($response);
    }
}
