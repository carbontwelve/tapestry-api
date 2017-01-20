<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Output\NullOutput;

class ExampleController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->bootProject(new NullOutput());

        /** @var \Tapestry\Modules\ContentTypes\ContentTypeFactory $contentTypes */
        $contentTypes = $this->project['content_types'];

        $output = [];

        /** @var \Tapestry\Entities\ContentType $contentType */
        foreach ($contentTypes->all() as $contentType) {
            $tmp = new \stdClass();
            $tmp->type = "content-type";
            $tmp->attributes = [
                'name' => $contentType->getName(),
                'path' => $contentType->getPath(),
                'template' => $contentType->getTemplate(),
                'taxonomies' => array_keys($contentType->getTaxonomies()),
                'fileCount' => count($contentType->getFileList())
            ];
            array_push($output, $tmp);
        }
        $jsonResponse = new JsonRenderer($output);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }
}
