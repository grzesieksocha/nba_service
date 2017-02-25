<?php declare(strict_types = 1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LeagueOptionsRepository")
 * @ORM\Table(name="league_options")
 * @ORM\HasLifecycleCallbacks()
 */
class LeagueOptions
{
    const V_ACTIVE = true;
    const V_DISABLED = false;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\League", inversedBy="options")
     */
    private $league;

    /**
     * @ORM\Column(name="do_count_points", type="boolean", nullable=false, options={"default":true})
     */
    private $doCountPoints;

    /**
     * @ORM\Column(name="do_count_rebounds", type="boolean", nullable=false, options={"default":true})
     */
    private $doCountRebounds;

    /**
     * @ORM\Column(name="do_count_assists", type="boolean", nullable=false, options={"default":true})
     */
    private $doCountAssists;

    /**
     * @ORM\Column(name="do_count_blocks", type="boolean", nullable=false, options={"default":false})
     */
    private $doCountBlocks;

    /**
     * @ORM\Column(name="do_count_steals", type="boolean", nullable=false, options={"default":false})
     */
    private $doCountSteals;

    /**
     * @ORM\Column(name="first_round_multiplier", type="integer", nullable=false, options={"default":1})
     */
    private $firstRoundMultiplier;

    /**
     * @ORM\Column(name="second_round_multiplier", type="integer", nullable=false, options={"default":1})
     */
    private $secondRoundMultiplier;

    /**
     * @ORM\Column(name="third_round_multiplier", type="integer", nullable=false, options={"default":1})
     */
    private $thirdRoundMultiplier;

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
     * @return League
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param League $league
     *
     * @return LeagueOptions
     */
    public function setLeague(League $league)
    {
        $this->league = $league;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDoCountPoints()
    {
        return $this->doCountPoints;
    }

    /**
     * @param bool $doCountPoints
     *
     * @return LeagueOptions
     */
    public function setDoCountPoints(bool $doCountPoints)
    {
        $this->doCountPoints = $doCountPoints;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDoCountRebounds()
    {
        return $this->doCountRebounds;
    }

    /**
     * @param bool $doCountRebounds
     * @return LeagueOptions
     */
    public function setDoCountRebounds(bool $doCountRebounds)
    {
        $this->doCountRebounds = $doCountRebounds;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDoCountAssists()
    {
        return $this->doCountAssists;
    }

    /**
     * @param bool $doCountAssists
     *
     * @return LeagueOptions
     */
    public function setDoCountAssists(bool $doCountAssists)
    {
        $this->doCountAssists = $doCountAssists;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDoCountBlocks()
    {
        return $this->doCountBlocks;
    }

    /**
     * @param bool $doCountBlocks
     *
     * @return LeagueOptions
     */
    public function setDoCountBlocks(bool $doCountBlocks)
    {
        $this->doCountBlocks = $doCountBlocks;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDoCountSteals()
    {
        return $this->doCountSteals;
    }

    /**
     * @param bool $doCountSteals
     *
     * @return LeagueOptions
     */
    public function setDoCountSteals(bool $doCountSteals)
    {
        $this->doCountSteals = $doCountSteals;
        return $this;
    }

    /**
     * @return int
     */
    public function getFirstRoundMultiplier()
    {
        return $this->firstRoundMultiplier;
    }

    /**
     * @param int $firstRoundMultiplier
     * @return LeagueOptions
     */
    public function setFirstRoundMultiplier(int $firstRoundMultiplier)
    {
        $this->firstRoundMultiplier = $firstRoundMultiplier;
        return $this;
    }

    /**
     * @return int
     */
    public function getSecondRoundMultiplier()
    {
        return $this->secondRoundMultiplier;
    }

    /**
     * @param int $secondRoundMultiplier
     * @return LeagueOptions
     */
    public function setSecondRoundMultiplier(int $secondRoundMultiplier)
    {
        $this->secondRoundMultiplier = $secondRoundMultiplier;
        return $this;
    }

    /**
     * @return int
     */
    public function getThirdRoundMultiplier()
    {
        return $this->thirdRoundMultiplier;
    }

    /**
     * @param int $thirdRoundMultiplier
     *
     * @return LeagueOptions
     */
    public function setThirdRoundMultiplier(int $thirdRoundMultiplier)
    {
        $this->thirdRoundMultiplier = $thirdRoundMultiplier;
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
     * @param $lastChangeOn
     *
     * @return LeagueOptions
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
     * @return LeagueOptions
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

