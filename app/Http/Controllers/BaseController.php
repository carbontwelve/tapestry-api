<?php

namespace App\Http\Controllers;

use App\Factories\TapestryCoreFactory;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

    protected function bootProject(OutputInterface $output)
    {
        /** @var TapestryCoreFactory $tapestryFactory */
        $tapestryFactory = $this->container->get(TapestryCoreFactory::class);
        /** @var \Tapestry\Tapestry $tapestry */
        $tapestry = $tapestryFactory->build([
            '--env' => 'local',
            '--site-dir' => APP_BASE . '/test-site',
            '--dist-dir' => APP_BASE . '/storage/dist-local'
        ]);

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
}
