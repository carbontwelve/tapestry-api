<?php

namespace App\Providers;

use Interop\Container\ContainerInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
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

        $tapestry = new Tapestry();




        $tapestry = new Tapestry([
            'environment' => 'local',
            'cwd' => APP_BASE . '/test-site/'
        ]);

        $tapestry['paths.dist'] = APP_BASE . '/storage/dist-local';

        $tapestry->register(ContentServiceProvider::class);
        $tapestry->register(FilesystemServiceProvider::class);
        $tapestry->register(CommandServiceProvider::class);
        $tapestry->register(KernelServiceProvider::class);

        $pimple[Tapestry::class] = $tapestry;
    }
}
