<?php declare(strict_types = 1);

namespace AppBundle\Service;

use AppBundle\Entity\Match;
use AppBundle\Entity\Player;
use AppBundle\Entity\Statistics;

/**
 * Class StatsProvider
 * @package AppBundle\Service
 */
class StatsProvider
{
    /**
     * @param Player $player
     * @param Match $match
     *
     * @return Statistics
     */
    public function getStats(Player $player, Match $match)
    {

        return $stats;
    }
}