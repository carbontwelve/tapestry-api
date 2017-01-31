<?php

namespace App\Resources;

use App\AbstractResource;
use App\Entity\Project;

// http://blog.sub85.com/slim-3-with-doctrine-2.html
class ProjectResource extends AbstractResource
{
    public function get($id = null)
    {
        if (is_null($id)) {
            /** @var Project[]|null $records */
            if ($records = $this->entityManager->getRepository(Project::class)->findAll()) {
                return array_map(
                    function ($record) {
                        /** @var Project $record */
                        return $record->getArrayCopy();
                    },
                    $records
                );
            }
            return null;
        }
        /** @var Project|null $record */
        if ($record = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $id])) {
            return $record->getArrayCopy();
        }
        return null;
    }

    public function findByName($name) {
        /** @var Project|null $record */
        if ($record = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name])) {
            return $record->getArrayCopy();
        }
        return null;
    }

    public function save(Project $project) {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function delete(Project $project) {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }

    public function count()
    {
        return count($this->get());
    }
}