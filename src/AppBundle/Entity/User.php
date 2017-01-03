<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
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
     * Many Users have Many Leagues
     *
     * @ORM\ManyToMany(targetEntity="League", inversedBy="users")
     * @ORM\JoinTable(name="users_leagues")
     */
    protected $leagues;

    public function __construct()
    {
        $this->leagues = new ArrayCollection();
        parent::__construct();
    }
}