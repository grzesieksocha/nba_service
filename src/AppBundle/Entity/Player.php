<?php declare(strict_types = 1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 * @ORM\Table(name="player")
 * @ORM\HasLifecycleCallbacks()
 */
class Player
{
    const V_ACTIVE = 1;
    const V_DISABLED = 0;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=64)
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=64)
     */
    private $lastName;

    /**
     * @ORM\Column(name="short", type="smallint", length=3)
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team", inversedBy="players")
     */
    private $team;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Statistics", mappedBy="player")
     */
    private $statistics;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pick", mappedBy="player")
     */
    protected $picks;

    /**
     * @ORM\Column(name="last_change_on", type="datetime")
     */
    private $lastChangeOn;

    /**
     * @ORM\Column(name = "is_active", type = "boolean")
     */
    private $isActive;

    /**
     * Constructor...
     */
    public function __construct()
    {
        $this->statistics = new ArrayCollection();
        $this->picks = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return Player
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return Player
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return Player
     */
    public function setNumber(int $number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @param Team $team
     * @return Player
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return ArrayCollection|Statistics[]
     */
    public function getStatistics()
    {
        return $this->statistics;
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
     * @return Player
     */
    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return ArrayCollection|Pick[]
     */
    public function getPicks()
    {
        return $this->picks;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateLastChangedOn()
    {
        $this->setLastChangeOn(new DateTime());
    }

    /**
     * @param DateTime $lastChangeOn
     */
    public function setLastChangeOn(DateTime $lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     * @return DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }
}

