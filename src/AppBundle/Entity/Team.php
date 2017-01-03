<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name="teams")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(name="short", type="string", length=3)
     */
    private $short;

    /**
     * @ORM\Column(name="first_name", type="string", length=64)
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=64)
     */
    private $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Division", inversedBy="teams")
     */
    private $division;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Conference", inversedBy="teams")
     */
    private $conference;

    /**
     * @ORM\Column(name="site_name", type="string", length=64)
     */
    private $siteName;

    /**
     * @ORM\Column(name="city", type="string", length=32)
     */
    private $city;

    /**
     * @ORM\Column(name="state", type="string", length=32)
     */
    private $state;

    /**
     * @ORM\Column(name="last_change_on", type="datetime")
     */
    private $lastChangeOn;

    /**
     * @ORM\Column(name = "item_status", type = "boolean", options = {"default" = 1})
     */
    private $itemStatus;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setShort($short)
    {
        $this->short = $short;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    public function setDivision($division)
    {
        $this->division = $division;
    }

    public function getDivision()
    {
        return $this->division;
    }

    public function setConference($conference)
    {
        $this->conference = $conference;
    }

    public function getConference()
    {
        return $this->conference;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getSiteName()
    {
        return $this->siteName;
    }

    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
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

