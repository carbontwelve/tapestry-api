<?php

namespace App\Definitions;

use Slim\Container;
use Symfony\Component\Yaml\Yaml;
use Tapestry\Entities\ContentType;
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
     * @var \App\Entity\Project
     */
    private $project;

    /**
     * ContentType constructor.
     * @param ProjectFileInterface|\Tapestry\Entities\File $file
     * @param \App\Entity\Project $project
     * @param Container $container
     */
    public function __construct(ProjectFileInterface $file, \App\Entity\Project $project, Container $container)
    {
        $this->project = $project;
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

        $this->setAttribute('name', $file->getFilename());
        $this->setAttribute('ext', $file->getExt());
        $this->setAttribute('path', $file->getPath());
        $this->setAttribute('contentType', $file->getData('contentType', 'default'));
        $this->setLink('self', $this->container->get('router')->pathFor('project.file', [
            'id' => $this->getId(),
            'project' => $this->project->getId()
        ]));
        $this->setLink('contentType', $this->container->get('router')->pathFor('content-type.view', [
            'contentType' => $this->getAttribute('contentType'),
            'project' => $this->project->getId()
        ]));

        //
        // Merge Frontmatter
        //
        $frontMatter = new FrontMatter($this->file->getFileContent());
        $frontMatterData = $frontMatter->getData();
        $frontMatterData['title'] = $this->file->getData('title', ((isset($frontMatterData['title'])) ? $frontMatterData['title'] : $this->file->getFilename()));

        $date = $this->file->getData('date', $this->file->getLastModified());
        if ($date instanceof \DateTime) {
            $date = $date->getTimestamp();
        }

        $this->setAttribute('date', $date);
        $this->setAttribute('last_modified', $this->file->getLastModified());
        $this->setAttribute('fileContent', $frontMatter->getContent());
        $this->setAttribute('frontMatter', $frontMatterData);
        $this->setAttribute('slug', $file->getData('slug'));
        $this->setAttribute('permalink', ['raw' => $this->file->getData('permalink', $this->file->getData('content_type_permalink')), 'compiled' => $this->file->getCompiledPermalink()]);
    }

    public function merge($json)
    {
        $clone = clone($this);
        $clone->setAttribute('fileContent', $json['attributes']['fileContent']);
        $clone->setAttribute('frontMatter', array_merge($this->attributes['frontMatter'], $json['attributes']['frontMatter']));
        return $clone;
    }

    public function save()
    {
        $frontMatter = array_filter($this->getAttribute('frontMatter'), function($value) {
            return ! is_null($value);
        });
        $fileContent =
            "---\n" .
            Yaml::dump($frontMatter) .
            "---\n\n" .
            $this->getAttribute('fileContent');

        return file_put_contents($this->file->getFileInfo()->getPathname(), $fileContent);
    }

    public function withDirectoryRelationship()
    {
        $clone = clone($this);
        $clone->setRelationship(new Path($this->attributes['path'], $this->container));
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