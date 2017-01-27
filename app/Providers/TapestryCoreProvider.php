<?php

namespace App\Providers;

use App\Factories\TapestryCoreFactory;
use Interop\Container\ContainerInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
        $pimple[TapestryCoreFactory::class] = new TapestryCoreFactory();
    }
}
