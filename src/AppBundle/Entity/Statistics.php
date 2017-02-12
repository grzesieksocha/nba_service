<?php declare(strict_types = 1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * Statistics
 *
 * @ORM\Table(name="statistics")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatisticsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Statistics
{
    const V_ACTIVE = true;
    const V_DISABLED = false;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player", inversedBy="statistics")
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Match", inversedBy="statistics")
     */
    private $match;

    /**
     * @ORM\Column(name="is_starter", type="boolean")
     */
    private $isStarter;

    /**
     * @ORM\Column(name="points", type="integer")
     */
    private $points;

    /**
     * @ORM\Column(name="rebounds", type="integer")
     */
    private $rebounds;

    /**
     * @ORM\Column(name="assists", type="integer")
     */
    private $assists;

    /**
     * @ORM\Column(name="blocks", type="integer")
     */
    private $blocks;

    /**
     * @ORM\Column(name="steals", type="integer")
     */
    private $steals;

    /**
     * @ORM\Column(name="turnovers", type="integer")
     */
    private $turnovers;

    /**
     * @ORM\Column(name="minutes", type="string", length=5)
     */
    private $minutes;

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
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param Player $player
     *
     * @return Statistics
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
     * @return Statistics
     */
    public function setMatch(Match $match)
    {
        $this->match = $match;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsStarter()
    {
        return $this->isStarter;
    }

    /**
     * @param bool $isStarter
     * @return Statistics
     */
    public function setIsStarter(bool $isStarter)
    {
        $this->isStarter = $isStarter;
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
     * @return Statistics
     */
    public function setPoints(int $points)
    {
        $this->points = $points;
        return $this;
    }

    /**
     * @return int
     */
    public function getRebounds()
    {
        return $this->rebounds;
    }

    /**
     * @param int $rebounds
     *
     * @return Statistics
     */
    public function setRebounds(int $rebounds)
    {
        $this->rebounds = $rebounds;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssists()
    {
        return $this->assists;
    }

    /**
     * @param int $assists
     *
     * @return Statistics
     */
    public function setAssists(int $assists)
    {
        $this->assists = $assists;
        return $this;
    }

    /**
     * @return int
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param int $blocks
     *
     * @return Statistics
     */
    public function setBlocks(int $blocks)
    {
        $this->blocks = $blocks;
        return $this;
    }

    /**
     * @return int
     */
    public function getSteals()
    {
        return $this->steals;
    }

    /**
     * @param int $steals
     *
     * @return Statistics
     */
    public function setSteals(int $steals)
    {
        $this->steals = $steals;
        return $this;
    }

    /**
     * @return int
     */
    public function getTurnovers()
    {
        return $this->turnovers;
    }

    /**
     * @param int $turnovers
     *
     * @return Statistics
     */
    public function setTurnovers(int $turnovers)
    {
        $this->turnovers = $turnovers;
        return $this;
    }

    /**
     * @return string
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @param string $minutes
     *
     * @return Statistics
     */
    public function setMinutes(string $minutes)
    {
        $this->minutes = $minutes;
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
     * @return Statistics
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

