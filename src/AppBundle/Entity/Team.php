<?php declare(strict_types = 1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 * @ORM\Table(name="team")
 * @ORM\HasLifecycleCallbacks()
 */
class Team
{
    const V_ACTIVE = true;
    const V_DISABLED = false;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Player", mappedBy="team")
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Match", mappedBy="homeTeam")
     */
    private $matchesHome;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Match", mappedBy="awayTeam")
     */
    private $matchesAway;

    /**
     * @ORM\Column(name="last_change_on", type="datetime")
     */
    private $lastChangeOn;

    /**
     * @ORM\Column(name = "is_active", type = "boolean")
     */
    private $isActive;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->matchesHome = new ArrayCollection();
        $this->matchesAway = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
     return $this->getFullName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
     return ucwords($this->getFirstName() . ' ' . $this->getLastName());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $short
     * @return Team
     */
    public function setShort(string $short)
    {
        $this->short = $short;
        return $this;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param DateTime $lastChangeOn
     * @return Team
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
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
     * @param string $division
     * @return Team
     */
    public function setDivision(string $division)
    {
        $this->division = $division;
        return $this;
    }

    /**
     * @return string
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * @param string $conference
     * @return Team
     */
    public function setConference(string $conference)
    {
        $this->conference = $conference;
        return $this;
    }

    /**
     * @return string
     */
    public function getConference()
    {
        return $this->conference;
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
     * @return Team
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
     * @return Team
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @param string $siteName
     * @return Team
     */
    public function setSiteName(string $siteName)
    {
        $this->siteName = $siteName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Team
     */
    public function setCity(string $city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Team
     */
    public function setState(string $state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return ArrayCollection|Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @return ArrayCollection|Match[]
     */
    public function getMatchesHome()
    {
        return $this->matchesHome;
    }

    /**
     * @return ArrayCollection|Match[]
     */
    public function getMatchesAway()
    {
        return $this->matchesAway;
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
     * @return Team
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

