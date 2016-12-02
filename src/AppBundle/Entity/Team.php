<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="teams")
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="short", type="string", length=3, unique=true)
     */
    private $short;

    /**
     * @ORM\Column(name="name", type="string", length=64, unique=true)
     */
    private $name;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $short
     *
     * @return Team
     */
    public function setShort($short)
    {
        $this->short = $short;

        return $this;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param string $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

