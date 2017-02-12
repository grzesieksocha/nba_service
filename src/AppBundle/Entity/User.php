<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LeagueHasUser", mappedBy="user")
     */
    protected $leagueHasUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pick", mappedBy="user")
     */
    protected $picks;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->picks = new ArrayCollection();
        $this->leagueHasUser = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|LeagueHasUser[]
     */
    public function getLeagueHasUsers()
    {
        return $this->leagueHasUser;
    }

    /**
     * @return ArrayCollection|Pick[]
     */
    public function getPicks()
    {
        return $this->picks;
    }

    /**
     * @return League[]
     */
    public function getLeagues()
    {
        $leagues = [];
        foreach ($this->getLeagueHasUsers() as $leagueHasUser) {
            $leagues[] = $leagueHasUser->getLeague();
        }

        return $leagues;
    }
}