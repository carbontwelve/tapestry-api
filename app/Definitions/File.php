<?php

namespace App\Definitions;

use Slim\Container;
use Tapestry\Entities\Project;
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

        $this->setAttribute('path', $file->getPath());
        $this->setLink('self', $this->container->get('router')->pathFor('filesystem.file', [
            'id' => $this->getId()
        ]));

        $this->setRelationship(new Directory($this->attributes['path'], $this->container));
    }

    public function setLink($name, $url)
    {
        // Files do not have related links
        if ($name === 'related') {
            return;
        }
        parent::setLink($name, $url);
    }

    public function withDetails()
    {
        // We only care about the frontmatter stored inside the file, not the frontmatter as mutated by Tapestry
        $frontMatter = new FrontMatter($this->file->getFileContent());
        $this->setAttribute('fileContent', $frontMatter->getContent());
        $this->setAttribute('frontMatter', $frontMatter->getData());
    }
}