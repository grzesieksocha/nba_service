<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name="divisions")
 * @ORM\HasLifecycleCallbacks()
 */
class Division
{
    const V_ITEM_STATUS_ACTIVE = 1;
    const V_ITEM_STATUS_DISABLED = 0;

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

    /**
     * @ORM\Column(name = "item_status", type = "boolean", options = {"default" = 1})
     */
    private $itemStatus;

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

    public function addTeam($team)
    {
        $this->teams->add($team);
    }

    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    public function setItemStatus($itemStatus)
    {
        $this->itemStatus = $itemStatus;
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