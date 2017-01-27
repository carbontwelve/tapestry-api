<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="projects", uniqueConstraints={@ORM\UniqueConstraint(name="project_id", columns={"id"})}))
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $path;

    /**
     * Get project id
     *
     * @ORM\return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get project name
     *
     * @ORM\return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get project name
     *
     * @ORM\return string
     */
    public function getpath()
    {
        return $this->path;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get array copy of object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->getArrayCopy());
    }
}