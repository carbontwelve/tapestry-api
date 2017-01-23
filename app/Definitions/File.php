<?php

namespace App\Definitions;

use Slim\Container;
use Tapestry\Entities\ProjectFileInterface;
use Tapestry\Modules\Content\FrontMatter;

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
    public function hydrate(ProjectFileInterface $file)
    {
        $this->id = $file->getUid();
        $this->type = 'file';
        $this->file = $file;

        $this->setAttribute('ext', $file->getExt());
        $this->setAttribute('path', $file->getPath());
        $this->setAttribute('contentType', $file->getData('contentType', 'default'));
        $this->setLink('self', $this->container->get('router')->pathFor('project.file', [
            'id' => $this->getId()
        ]));

        $frontMatter = new FrontMatter($this->file->getFileContent());
        $this->setAttribute('fileContent', $frontMatter->getContent());
        $this->setAttribute('frontMatter', $frontMatter->getData());
    }

    public function withDirectoryRelationship()
    {
        $clone = clone($this);
        //$clone->setRelationship(new Directory($this->attributes['path'], $this->container));
        return $clone;
    }

    public function setLink($name, $url)
    {
        // Files do not have related links
        if ($name === 'related') {
            return;
        }
        parent::setLink($name, $url);
    }
}