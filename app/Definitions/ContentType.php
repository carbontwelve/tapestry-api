<?php

namespace App\Definitions;

use Slim\Container;
use \Tapestry\Entities\ContentType as TapestryContentType;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFileInterface;

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
        $this->setAttribute('files', array_keys($contentType->getFileList()));
        $this->setAttribute('fileCount', count($contentType->getFileList()));

        $this->setLink('self', $this->container->get('router')->pathFor('content-type.view', [
            'contentType' => $contentType->getName()
        ]));

        foreach($contentType->getTaxonomies() as $taxonomy) {
            $tmpTaxonomy = new Taxonomy($taxonomy, $this->container);
            $tmpTaxonomy->setLink('related', $this->container->get('router')->pathFor('content-type.taxonomy', [
                'contentType' => $contentType->getName(),
                'taxonomy' => $taxonomy->getName()
            ]));
            $this->setRelationship($tmpTaxonomy);
        }

        $this->relationships['files'] = [];

        foreach(array_keys($contentType->getFileList()) as $file) {
            /** @var Project $project */
            $project = $this->container->get(Project::class);

            if (! $file = $project['files.' . $file]){
                continue;
            }
            /** @var ProjectFileInterface $file */

            $tmpFile = new File($file, $this->container);
            $this->setRelationship($tmpFile);
        }
    }

}