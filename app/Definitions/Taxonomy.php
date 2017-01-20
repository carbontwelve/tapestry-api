<?php

namespace App\Definitions;

use Slim\Container;
use Tapestry\Entities\Taxonomy as TapestryTaxonomy;

class Taxonomy extends JsonDefinition
{
    /**
     * @var Container
     */
    private $container;

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

        foreach ($taxonomy->getFileList() as $id => $fileList) {
            $classification = new Classification($id, $fileList, $this->container);
            if (isset($this->links['related'])) {
                $classification->setLink('related', $this->links['related'] . '/' . $id);
            }
            $this->setRelationship($classification);
        }
    }
}