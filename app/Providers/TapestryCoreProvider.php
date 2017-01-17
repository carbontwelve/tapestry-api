<?php

namespace App\Providers;

use Interop\Container\ContainerInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Providers\CommandServiceProvider;
use Tapestry\Providers\ContentServiceProvider;
use Tapestry\Providers\FilesystemServiceProvider;
use Tapestry\Providers\KernelServiceProvider;
use Tapestry\Tapestry;

class TapestryCoreProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container|ContainerInterface $pimple A container instance
     *
     */
    public function register(Container $pimple)
    {
        $tapestry = new Tapestry(new ArrayInput([
            '--env' => 'local',
            '--site-dir' => APP_BASE . '/test-site/',
            '--dist-dir' => APP_BASE . '/storage/dist-local'
        ]));
        $pimple[Tapestry::class] = $tapestry;
    }
}
