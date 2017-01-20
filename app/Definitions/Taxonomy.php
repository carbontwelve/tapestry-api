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

    private $taxonomy;

    /**
     * ContentType constructor.
     * @param TapestryTaxonomy $taxonomy
     * @param Container $container
     */
    public function __construct(TapestryTaxonomy $taxonomy, Container $container)
    {
        $this->container = $container;
        $this->taxonomy = $taxonomy;
        $this->hydrate($taxonomy);
    }

    public function hydrate (TapestryTaxonomy $taxonomy) {
        $this->id = $taxonomy->getName();
        $this->type = 'taxonomy';
        $this->setAttribute('classificationCount', count($taxonomy->getFileList()));
    }

    public function withClassificationRelationship($closure = null)
    {
        $clone = clone($this);

        foreach ($this->taxonomy->getFileList() as $id => $fileList) {
            $classification = new Classification($id, $fileList, $this->container);
            if (isset($this->links['related'])) {
                $classification->setLink('related', $this->links['related'] . '/' . $id);
            }

            if (! is_null($closure) && $closure instanceof \Closure){
                $classification = $closure($classification);
                if (! $classification instanceof Classification){
                    throw new \Exception('The closure passed to withClassificationRelationship must return an instance of Classification.');
                }
            }

            $clone->setRelationship($classification);
        }

        return $clone;
    }
}