<?php

namespace App\Definitions;

use Slim\Container;
use Tapestry\Entities\Taxonomy as TapestryTaxonomy;

class Taxonomy extends JsonDefinition
{
    /**
     * ContentType constructor.
     * @param TapestryTaxonomy $taxonomy
     * @param Container $container
     */
    public function __construct(TapestryTaxonomy $taxonomy, Container $container)
    {
        $this->container = $container;
        $this->hydrate($taxonomy);
    }

    public function hydrate (TapestryTaxonomy $taxonomy) {
        $this->id = $taxonomy->getName();
        $this->type = 'taxonomy';
        $this->setAttribute('classificationCount', count($taxonomy->getFileList()));
    }

}