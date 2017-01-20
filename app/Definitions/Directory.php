<?php

namespace App\Definitions;

use Slim\Container;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFileInterface;

class Directory extends JsonDefinition
{

    /**
     * @var Container
     */
    private $container;

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

    /**
     * @param string $path
     */
    public function hydrate($path)
    {
        $this->id = base64_encode($path);
        $this->type = 'directory';

        $this->setAttribute('path', $path);

        $this->setLink('self', $this->container->get('router')->pathFor('filesystem.directory', [
            'id' => $this->getId()
        ]));
    }

    public function withDirectoriesRelationship($closure = null)
    {

        /** @var Project $project */
        $project = $this->container->get(Project::class);
        $directory = realpath($project->sourceDirectory . DIRECTORY_SEPARATOR . $this->attributes['path']);

        $finder = new Finder();
        $finder->directories()
            ->followLinks()
            ->in($directory)
            ->ignoreDotFiles(true)
            ->depth('==0');

        $clone = clone($this);
        $directories = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $directory */
        foreach ($finder->directories() as $directory) {
            // @todo $directoryPath should not start with a / or \
            $directoryPath = $this->attributes['path'] . DIRECTORY_SEPARATOR . $directory->getRelativePathname();
            array_push($directories, $directoryPath);
        }

        return $clone;
    }

    public function withFilesRelationship($closure = null)
    {
        /** @var Project $project */
        $project = $this->container->get(Project::class);
        $directory = realpath($project->sourceDirectory . DIRECTORY_SEPARATOR . $this->attributes['path']);

        $finder = new Finder();
        $finder->files()
            ->followLinks()
            ->in($directory)
            ->ignoreDotFiles(true)
            ->depth('==0');

        $clone = clone($this);

        $files = [];
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder->files() as $file) {
            array_push($files,
                $this->cleanUid($this->attributes['path'] . DIRECTORY_SEPARATOR . $file->getRelativePathname()));
        }

        foreach ($files as $key => $value) {
            /** @var ProjectFileInterface $file */
            if (!$file = $project['files.' . $value]) {
                unset($files[$key]);
                continue;
            }

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

            if (!is_null($closure) && $closure instanceof \Closure) {
                $file = $closure($file);
                if (!$file instanceof File) {
                    throw new \Exception('The closure passed to withFilesRelationship must return an instance of File.');
                }
            }
            $clone->setRelationship($file);
        }

        $clone->setAttribute('files', array_values($files));
        $clone->setAttribute('fileCount', count($clone->attributes['files']));

        return $clone;
    }

    private function cleanUid($uid)
    {
        $uid = str_replace('.', '_', $uid);
        $uid = str_replace(['/', '\\'], '_', $uid);

        if (substr($uid, 0, 1) === '_') {
            $uid = substr($uid, 1);
        }

        return $uid;
    }

}