<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PickRepository")
 * @ORM\Table(name = "pick")
 * @ORM\HasLifecycleCallbacks()
 */
class Pick
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="picks")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\League", inversedBy="picks")
     */
    private $league;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player", inversedBy="picks")
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Match")
     */
    private $match;

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer")
     */
    private $points;

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
        $this->statistics = new ArrayCollection();
    }

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
     * @return Pick
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
     * @return Pick
     */
    public function setLeague(League $league)
    {
        $this->league = $league;
        return $this;
    }

    /**
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param Player $player
     *
     * @return Pick
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @return Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param Match $match
     *
     * @return Pick
     */
    public function setMatch(Match $match)
    {
        $this->match = $match;
        return $this;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param int $points
     *
     * @return Pick
     */
    public function setPoints(int $points)
    {
        $this->points = $points;
        return $this;
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
     * @return Pick
     */
    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * @param DateTime $lastChangeOn
     */
    private function setLastChangeOn(DateTime $lastChangeOn)
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