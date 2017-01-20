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

    private $contentType;

    /**
     * ContentType constructor.
     * @param TapestryContentType $contentType
     * @param Container $container
     */
    public function __construct(TapestryContentType $contentType, Container $container)
    {
        $this->container = $container;
        $this->contentType = $contentType;
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
    }

    public function withTaxonomiesRelationship($closure = null)
    {
        $clone = clone($this);
        foreach($this->contentType->getTaxonomies() as $taxonomy) {
            $tmpTaxonomy = new Taxonomy($taxonomy, $this->container);
            $tmpTaxonomy->setLink('related', $this->container->get('router')->pathFor('content-type.taxonomy', [
                'contentType' => $this->contentType->getName(),
                'taxonomy' => $taxonomy->getName()
            ]));

            if (! is_null($closure) && $closure instanceof \Closure){
                $tmpTaxonomy = $closure($tmpTaxonomy);
                if (! $tmpTaxonomy instanceof Taxonomy){
                    throw new \Exception('The closure passed to withTaxonomiesRelationship must return an instance of Taxonomy.');
                }
            }

            $clone->setRelationship($tmpTaxonomy);
        }
        return $clone;
    }

    public function withFilesRelationship($closure = null)
    {
        $clone = clone($this);

        $clone->relationships['files'] = [];

        foreach(array_keys($this->contentType->getFileList()) as $file) {
            /** @var Project $project */
            $project = $this->container->get(Project::class);

            if (! $file = $project['files.' . $file]){
                continue;
            }
            /** @var ProjectFileInterface $file */

            $tmpFile = new File($file, $this->container);

            if (! is_null($closure) && $closure instanceof \Closure){
                $tmpFile = $closure($tmpFile);
                if (! $tmpFile instanceof File){
                    throw new \Exception('The closure passed to withFilesRelationship must return an instance of File.');
                }
            }


            $clone->setRelationship($tmpFile);
        }

        return $clone;
    }

}