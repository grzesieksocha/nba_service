<?php

namespace AppBundle\Entity;

use \DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="divisions")
 * @ORM\HasLifecycleCallbacks()
 */
class Division
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Team", mappedBy="division")
     */
    private $teams;

    /**
     * @ORM\Column(name="last_change_on", type="datetime")
     */
    private $lastChangeOn;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getTeams()
    {
        return $this->teams;
    }

    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateLastChangedOn()
    {
        $this->setLastChangeOn(new DateTime(date('Y-m-d H:i:s')));
    }
}