<?php

namespace App\Definitions;

use Slim\Container;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFileInterface;
use Tapestry\Entities\Taxonomy as TapestryTaxonomy;

//
// Taxonomy HasMany Classification
// Classification HasMany File
//
class Classification extends JsonDefinition
{
    /**
     * @var Container
     */
    private $container;

    /**
     * ContentType constructor.
     * @param string $id
     * @param array $fileList
     * @param Container $container
     */
    public function __construct($id, array $fileList = [], Container $container)
    {
        $this->container = $container;
        $this->hydrate($id, $fileList);
    }

    public function hydrate ($id, array $fileList = []) {
        $this->id = $id;
        $this->type = 'classification';
        $this->setAttribute('fileCount', count($fileList));
        $this->setAttribute('files', array_keys($fileList));
    }

    public function withFilesRelationship($closure = null)
    {
        $clone = clone($this);

        foreach($this->attributes['files'] as $file) {
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