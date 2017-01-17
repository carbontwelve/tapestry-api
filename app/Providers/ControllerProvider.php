<?php

namespace App\Providers;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ContentTypeController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\FilesystemController;
use Interop\Container\ContainerInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ControllerProvider implements ServiceProviderInterface
{
    /** @var array */
    private $controllers = [];

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
        $this->registerControllers($pimple);
        /**
         * @var string
         * @var \App\Http\Controllers\BaseController $controller
         */
        foreach ($this->controllers as $id => $controller) {
            $controller->setContainer($pimple);
            $pimple[$id] = $controller;
        }
    }

    /**
     * @param Container $pimple
     */
    private function registerControllers(Container $pimple)
    {
        $this->controllers['App\Http\Controllers\AuthenticationController'] = new AuthenticationController();
        $this->controllers['App\Http\Controllers\ContentTypeController'] = new ContentTypeController();
        $this->controllers['App\Http\Controllers\FilesystemController'] = new FilesystemController();

        $this->controllers['App\Http\Controllers\ExampleController'] = new ExampleController();
    }
}
