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

    public function __construct()
    {
        parent::__construct();

        $this->leagueHasUser = new ArrayCollection();
    }

//    /**
//     * @param LeagueHasUser $leagueHasUser
//     *
//     * @return User
//     */
//    public function addLeagueHasUser(LeagueHasUser $leagueHasUser)
//    {
//        $this->leagueHasUser->add($leagueHasUser);
//        return $this;
//    }

//    /**
//     * @param LeagueHasUser $leagueHasUser
//     *
//     * @return User
//     */
//    public function removeLeagueHasUser(LeagueHasUser $leagueHasUser)
//    {
//        $this->leagueHasUser->removeElement($leagueHasUser);
//        return $this;
//    }

    /**
     * @return ArrayCollection|LeagueHasUser[]
     */
    public function getLeagueHasUsers()
    {
        return $this->leagueHasUser;
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