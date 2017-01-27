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
            return $this->entityManager->getRepository(Project::class)->findAll();
        }
        return $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $id]);
    }

    public function count()
    {
        return count($this->get());
    }
}