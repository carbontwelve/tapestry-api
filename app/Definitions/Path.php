<?php

namespace App\Definitions;

use Slim\Container;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFileInterface;

class Path extends JsonDefinition
{

    /**
     * @var Container
     */
    private $container;

    private $path;

    /**
     * ContentType constructor.
     * @param string $path
     * @param Container $container
     */
    public function __construct($path, Container $container)
    {
        $this->container = $container;
        $this->hydrate($path);
    }

    public function hydrate($path)
    {
        $this->id = $path;
        $this->type = 'path';

        /** @var Project $project */
        $project = $this->container->get(Project::class);
        $path = $project->sourceDirectory . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($path)) {
            throw new \Exception('The path [' . $path . '] does not exist.');
        }

        if (DIRECTORY_SEPARATOR === '/') {
            $path = str_replace('\\', '/', $path);
            while( strpos( ($path=str_replace('//','/',$path)), '//' ) !== false );
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            $path = str_replace('/', '\\', $path);
            while( strpos( ($path=str_replace('\\\\','\\',$path)), '\\\\' ) !== false );
        }

        $this->path = $path;

        $this->setAttribute('name', pathinfo($path, PATHINFO_BASENAME));
        $this->setAttribute('directory', is_file($path) === false);

        $this->setLink('self', $this->container->get('router')->pathFor('filesystem.path', [
            'id' => base64_encode($this->id)
        ]));

        if (strpos($this->path, 'source') !== false) {
            $split = explode('source', $this->path);
            $sourcePath = $split[1];
            if (substr($sourcePath, 0, 1) === DIRECTORY_SEPARATOR) {
                $sourcePath = substr($sourcePath, 1);
            }

            if (is_file($this->path)) {
                $sourcePath = $this->cleanPath($sourcePath);
                if (isset($project['files.' . $sourcePath])) {
                    $this->setAttribute('projectFile', $sourcePath);
                }
            }

            if (! is_file($this->path)) {
                $split = explode(DIRECTORY_SEPARATOR, $sourcePath);
                array_pop($split);

                $parentPath = implode(DIRECTORY_SEPARATOR, $split);
                $parentLink = $this->container->get('router')->pathFor('filesystem.path', [
                    'id' => base64_encode($parentPath)
                ]);

                if ($parentLink !== $this->getLink('self')) {
                    $this->setLink('parent', $parentLink);
                }
            }
        }
    }

    /**
     * @return Path
     * @throws \Exception
     */
    public function withPathRelationship()
    {
        $clone = clone($this);
        if (!is_file($this->path)) {
            if (!$path = realpath($this->path)) {
                throw new \Exception('The path [' . $path . '] does not exist.');
            }

            $finder = new Finder();
            $finder
                ->followLinks()
                ->in($path)
                ->ignoreDotFiles(true)
                ->depth('==0');

            foreach ($finder as $file) {
                $clone->setRelationship(new Path($this->id . DIRECTORY_SEPARATOR . $file->getRelativePathname(),
                    $this->container));
            }
        }
        return $clone;
    }

    /**
     * @return Path
     */
    public function withProjectFileRelationship()
    {
        if (! isset($this->attributes['projectFile'])) {
            return $this;
        }

        /** @var Project $project */
        $project = $this->container->get(Project::class);
        if (! $file = $project['files.' . $this->attributes['projectFile']]) {
            return $this;
        }

        $clone = clone($this);
        $file = new File($file, $this->container);
        /** @var \App\Definitions\File $file */
        $file = $file->apply(function (JsonDefinition $definition) {
            foreach ($definition->getRelationships() as $key => $relationship) {
                if ($relationship->type === $this->type) {
                    $definition->unsetRelationship($key);
                }
            }
            return $definition;
        });

        $clone->setRelationship($file);
        $clone->setLink('related', $file->getLink('self'));
        return $clone;
    }

    private function cleanPath($path)
    {
        $path = str_replace('.', '_', $path);
        $path = str_replace(['/', '\\'], '_', $path);
        return $path;
    }
}