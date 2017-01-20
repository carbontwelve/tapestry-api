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

        foreach(array_keys($fileList) as $file) {
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