<?php

namespace App\Http\Controllers;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Modules\Content\LoadSourceFiles;
use Tapestry\Modules\ContentTypes\LoadContentTypes;
use Tapestry\Modules\ContentTypes\ParseContentTypes;
use Tapestry\Modules\Generators\LoadContentGenerators;
use Tapestry\Modules\Kernel\BootKernel;
use Tapestry\Modules\Renderers\LoadContentRenderers;
use Tapestry\Tapestry;

class BaseController
{
    /** @var null|ContainerInterface|\Slim\Container */
    protected $container;

    /** @var Tapestry */
    protected $tapestry;

    /** @var array  */
    protected $steps = [
        BootKernel::class,
        LoadContentTypes::class,
        LoadContentRenderers::class,
        LoadContentGenerators::class,
        LoadSourceFiles::class,
        ParseContentTypes::class,
    ];

    /**
     * @var Project
     */
    protected $project;

    protected function bootProject(OutputInterface $output)
    {
        $this->project = $this->tapestry->getContainer()->get(Project::class);
        $generator = new Generator($this->steps, $this->tapestry);
        $generator->generate($this->project, $output);
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        $this->tapestry = $this->container->get(Tapestry::class);
    }

    /**
     * @return ContainerInterface|null
     */
    public function getContainer()
    {
        return $this->container;
    }
}
