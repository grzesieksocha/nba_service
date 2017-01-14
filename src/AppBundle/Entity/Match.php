<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name = "game")
 * @ORM\HasLifecycleCallbacks()
 */
class Match
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
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="matchesHome")
     */
    private $homeTeam;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="matchesAway")
     */
    private $awayTeam;

    /**
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="home_team_points")
     */
    private $homeTeamPoints;

    /**
     * @var int
     *
     * @ORM\Column(name="away_team_points")
     */
    private $awayTeamPoints;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Statistics", mappedBy="match")
     */
    private $statistics;

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
     * @return Team
     */
    public function getHomeTeam()
    {
        return $this->homeTeam;
    }

    /**
     * @param Team $homeTeam
     *
     * @return Match
     */
    public function setHomeTeam(Team $homeTeam)
    {
        $this->homeTeam = $homeTeam;
        return $this;
    }

    /**
     * @return Team
     */
    public function getAwayTeam()
    {
        return $this->awayTeam;
    }

    /**
     * @param Team $awayTeam
     * @return Match
     */
    public function setAwayTeam(Team $awayTeam)
    {
        $this->awayTeam = $awayTeam;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return Match
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getHomeTeamPoints()
    {
        return $this->homeTeamPoints;
    }

    /**
     * @param int $homeTeamPoints
     *
     * @return Match
     */
    public function setHomeTeamPoints(int $homeTeamPoints)
    {
        $this->homeTeamPoints = $homeTeamPoints;
        return $this;
    }

    /**
     * @return int
     */
    public function getAwayTeamPoints()
    {
        return $this->awayTeamPoints;
    }

    /**
     * @param int $awayTeamPoints
     *
     * @return Match
     */
    public function setAwayTeamPoints(int $awayTeamPoints)
    {
        $this->awayTeamPoints = $awayTeamPoints;
        return $this;
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
     * @return Match
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