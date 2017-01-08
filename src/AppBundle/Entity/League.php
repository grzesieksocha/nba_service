<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name="league")
 * @ORM\HasLifecycleCallbacks()
 */
class League
{
    const V_ACTIVE = 1;
    const V_DISABLED = 0;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate;

    /**
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="LeagueHasUser", mappedBy="league")
     */
    private $leagueHasUser;

    /**
     * @ORM\Column(name="last_change_on", type="datetime")
     */
    private $lastChangeOn;

    /**
     * @ORM\Column(name = "is_active", type = "boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->leagueHasUser = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return League
     */
    public function setName(string $name)
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

    /**
     * @param string $description
     *
     * @return League
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param boolean $isPrivate
     *
     * @return League
     */
    public function setIsPrivate(bool $isPrivate)
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param string $password
     *
     * @return League
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return ArrayCollection|LeagueHasUser[]
     */
    public function getLeagueHasUsers()
    {
        return $this->leagueHasUser;
    }

//    /**
//     * @param LeagueHasUser $leagueHasUser
//     *
//     * @return League
//     */
//    public function addLeagueHasUser(LeagueHasUser $leagueHasUser)
//    {
//        $this->leagueHasUser->add($leagueHasUser);
//        return $this;
//    }
//
//    /**
//     * @param LeagueHasUser $leagueHasUser
//     */
//    public function removeLeagueHasUser(LeagueHasUser $leagueHasUser)
//    {
//        $this->leagueHasUser->removeElement($leagueHasUser);
//    }

    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * @param $lastChangeOn
     *
     * @return League
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return League
     */
    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateLastChangedOn()
    {
        $this->setLastChangeOn(new DateTime(date('Y-m-d H:i:s')));
    }

    public function getUsers()
    {
        $users = [];
        foreach ($this->getLeagueHasUsers() as $leagueHasUser) {
            $users[] = $leagueHasUser->getUser();
        }

        return $users;
    }
}

