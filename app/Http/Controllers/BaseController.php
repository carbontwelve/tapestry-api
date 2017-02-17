<?php

namespace App\Http\Controllers;

use App\Factories\TapestryCoreFactory;
use App\JsonRenderer;
use Interop\Container\ContainerInterface;
use Slim\Http\Response;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Modules\Content\LoadSourceFiles;
use Tapestry\Modules\ContentTypes\LoadContentTypes;
use Tapestry\Modules\Generators\LoadContentGenerators;
use Tapestry\Modules\Kernel\BootKernel;
use Tapestry\Modules\Renderers\LoadContentRenderers;

class BaseController
{
    /** @var null|ContainerInterface|\Slim\Container */
    protected $container;

    /** @var array  */
    protected $steps = [
        BootKernel::class,
        LoadContentTypes::class,
        LoadContentRenderers::class,
        LoadContentGenerators::class,
        LoadSourceFiles::class,
        //Tapestry\Modules\ContentTypes\ParseContentTypes:class,
    ];

    /**
     * @var Project
     */
    protected $project;

    protected function bootProject(OutputInterface $output, \App\Entity\Project $project)
    {
        /** @var TapestryCoreFactory $tapestryFactory */
        $tapestryFactory = $this->container->get(TapestryCoreFactory::class);
        /** @var \Tapestry\Tapestry $tapestry */
        $tapestry = $tapestryFactory->build([
            '--env' => 'local',
            //'--site-dir' => APP_BASE . '/test-site',
            '--site-dir' => $project->getPath(),
            '--dist-dir' => APP_BASE . '/storage/dist-local'
        ]);

        // I have set this here so that the API will return draft posts (otherwise Tapestry filters them out)
        // however this will need factoring out for project generation.
        $configuration = $tapestry->getContainer()->get(Configuration::class);
        $configuration->set('publish_drafts', true);

        $this->project = $tapestry->getContainer()->get(Project::class);
        $generator = new Generator($this->steps, $tapestry);
        $generator->generate($this->project, $output);
        $this->container[Project::class] = $this->project;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface|null
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Response $response
     * @param string $message
     * @param int $code
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    protected function abort(Response $response, $message, $code = 412)
    {
        $jsonResponse = new JsonRenderer([
            'error' => true,
            'message' => $message
        ]);
        return $jsonResponse->render($response, $code);
    }
}
