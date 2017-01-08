<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * LeagueHasUser
 *
 * @ORM\Table(name="league_has_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LeagueHasUserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class LeagueHasUser
{
    const V_ACTIVE = 1;
    const V_DISABLED = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="leagueHasUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\League", inversedBy="leagueHasUser")
     * @ORM\JoinColumn(name="league_id", referencedColumnName="id")
     */
    private $league;

    /**
     * @ORM\Column(name="is_league_admin", type="boolean")
     */
    private $isLeagueAdmin;

    /**
     * @ORM\Column(name="last_change_on", type="datetime")
     */
    private $lastChangeOn;

    /**
     * @ORM\Column(name = "is_active", type = "boolean")
     */
    private $isActive;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return LeagueHasUser
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return League
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param League $league
     *
     * @return LeagueHasUser
     */
    public function setLeague(League $league)
    {
        $this->league = $league;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsLeagueAdmin()
    {
        return $this->isLeagueAdmin;
    }

    /**
     * @param boolean $isLeagueAdmin
     * @return LeagueHasUser
     */
    public function setIsLeagueAdmin(bool $isLeagueAdmin)
    {
        $this->isLeagueAdmin = $isLeagueAdmin;
        return $this;
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
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return LeagueHasUser
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
}

