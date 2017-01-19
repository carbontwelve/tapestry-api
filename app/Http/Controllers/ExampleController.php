<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Modules\Content\LoadSourceFiles;
use Tapestry\Modules\ContentTypes\LoadContentTypes;
use Tapestry\Modules\ContentTypes\ParseContentTypes;
use Tapestry\Modules\Generators\LoadContentGenerators;
use Tapestry\Modules\Kernel\BootKernel;
use Tapestry\Modules\Renderers\LoadContentRenderers;

class ExampleController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        /** @var Project $project */
        $project = $this->tapestry->getContainer()->get(Project::class);
        $steps = [
            BootKernel::class,
            LoadContentTypes::class,
            LoadContentRenderers::class,
            LoadContentGenerators::class,
            LoadSourceFiles::class,
            ParseContentTypes::class,
        ];

        $generator = new Generator($steps, $this->tapestry);

        $generator->generate($project, new NullOutput());

        /** @var \Tapestry\Modules\ContentTypes\ContentTypeFactory $contentTypes */
        $contentTypes = $project['content_types'];

        $output = [];

        /** @var \Tapestry\Entities\ContentType $contentType */
        foreach ($contentTypes->all() as $contentType) {
            $tmp = new \stdClass();
            $tmp->type = "content-type";
            $tmp->attributes = [
                'name' => $contentType->getName(),
                'path' => $contentType->getPath(),
                'template' => $contentType->getTemplate(),
                'taxonomies' => []
            ];
            array_push($output, $tmp);
        }
        $jsonResponse = new JsonRenderer($output);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()
        ]);
        return $jsonResponse->render($response);
    }
}
