<?php

namespace App\Definitions;

use Slim\Container;
use Tapestry\Entities\ProjectFileInterface;

class File extends JsonDefinition
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var ProjectFileInterface|\Tapestry\Entities\File
     */
    private $file;

    /**
     * ContentType constructor.
     * @param ProjectFileInterface|\Tapestry\Entities\File $file
     * @param Container $container
     */
    public function __construct(ProjectFileInterface $file, Container $container)
    {
        $this->container = $container;
        $this->hydrate($file);
    }

    /**
     * @param ProjectFileInterface|\Tapestry\Entities\File $file
     */
    public function hydrate (ProjectFileInterface $file) {
        $this->id = $file->getUid();
        $this->type = 'file';
        $this->file = $file;

        $this->setLink('self', $this->container->get('router')->pathFor('filesystem.file', [
            'id' => $this->getId()
        ]));
    }
}