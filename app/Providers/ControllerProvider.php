<?php

namespace App\Providers;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ContentTypeController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\FilesystemController;
use App\Http\Controllers\ProjectController;
use App\Resources\ProjectResource;
use Doctrine\ORM\EntityManager;
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
            //$controller->setContainer($pimple);
            $pimple[$id] = $controller;
        }
    }

    /**
     * @param Container $pimple
     */
    private function registerControllers(Container $pimple)
    {
        $this->controllers['App\Http\Controllers\AuthenticationController'] = function(\Slim\Container $c) {
            $controller = new AuthenticationController(new ProjectResource($c->get(EntityManager::class)));
            $controller->setContainer($c);
            return $controller;
        };

        $this->controllers['App\Http\Controllers\ContentTypeController'] = function(\Slim\Container $c) {
            $controller = new ContentTypeController(new ProjectResource($c->get(EntityManager::class)));
            $controller->setContainer($c);
            return $controller;
        };

        $this->controllers['App\Http\Controllers\FilesystemController'] = function(\Slim\Container $c) {
            $controller = new FilesystemController(new ProjectResource($c->get(EntityManager::class)));
            $controller->setContainer($c);
            return $controller;
        };

        $this->controllers['App\Http\Controllers\ProjectController'] = function(\Slim\Container $c) {
            $controller = new ProjectController(new ProjectResource($c->get(EntityManager::class)));
            $controller->setContainer($c);
            return $controller;
        };

        $this->controllers['App\Http\Controllers\ExampleController'] = function(\Slim\Container $c) {
            $controller = new ExampleController();
            $controller->setContainer($c);
            return $controller;
        };
    }
}
