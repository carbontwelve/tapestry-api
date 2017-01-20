<?php

namespace App\Definitions;

use Slim\Container;
use \Tapestry\Entities\ContentType as TapestryContentType;

//
// Has Many Taxonomy
// Has Many File
// Has One Directory
//

class ContentType extends JsonDefinition
{
    /**
     * @var Container
     */
    private $container;

    /**
     * ContentType constructor.
     * @param TapestryContentType $contentType
     * @param Container $container
     */
    public function __construct(TapestryContentType $contentType, Container $container)
    {
        $this->container = $container;
        $this->hydrate($contentType);
    }

    public function hydrate(TapestryContentType $contentType)
    {
        $this->id = $contentType->getName();
        $this->type = 'content-type';

        $this->setAttribute('name', $contentType->getName());
        $this->setAttribute('path', $contentType->getPath());
        $this->setAttribute('template', $contentType->getTemplate());
        $this->setAttribute('taxonomies', array_keys($contentType->getTaxonomies()));
        $this->setAttribute('fileCount', count($contentType->getFileList()));

        foreach($contentType->getTaxonomies() as $taxonomy) {
            $tmpTaxonomy = new Taxonomy($taxonomy, $this->container);
            $tmpTaxonomy->setLink('related', $this->container->get('router')->pathFor('content-type.taxonomy', [
                'contentType' => $contentType->getName(),
                'taxonomy' => $taxonomy->getName()
            ]));
            $this->setRelationship($tmpTaxonomy);
        }
    }

}