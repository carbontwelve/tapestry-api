<?php

namespace App\Http\Controllers;

use Interop\Container\ContainerInterface;
use Tapestry\Tapestry;

class BaseController
{
    /** @var null|ContainerInterface */
    protected $container;

    /** @var  Tapestry */
    protected $tapestry;

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
