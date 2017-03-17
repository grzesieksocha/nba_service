<?php declare(strict_types = 1);

namespace AppBundle\Pick;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\LeagueOptions;
use AppBundle\Entity\Match;
use AppBundle\Entity\Pick;
use AppBundle\Entity\Player;
use AppBundle\Repository\StatisticsRepository;
use Doctrine\ORM\EntityRepository;

/**
 * Class PointsResolver
 * @package AppBundle\Pick
 */
class PointsResolver
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var StatisticsRepository|EntityRepository
     */
    private $statisticsRepository;

    /**
     * @param EntityManager $entityManager
     * @param EntityRepository $statisticsRepository
     * @internal param MatchRepository $matchRepository
     */
    public function __construct(EntityManager $entityManager, EntityRepository $statisticsRepository)
    {
        $this->entityManager = $entityManager;
        $this->statisticsRepository = $statisticsRepository;
    }
    /**
     * @param Pick $pick
     */
    public function updatePoints(Pick $pick)
    {
        #TODO apply cache here!
        $league = $pick->getLeague();
        $options = $league->getOptions();
        $points = $this->getPoints($pick->getMatch(), $pick->getPlayer(), $league->getOptions());
        $pick->setPoints($points);
        $this->entityManager->persist($pick);
    }

    /**
     * @param Match $match
     * @param Player $player
     * @param LeagueOptions $leagueOptions
     *
     * @return int
     */
    private function getPoints(Match $match, Player $player, LeagueOptions $leagueOptions)
    {
        $points = 0;
        #TODO every match should have level
        $options = $this->buildOptionsArray($leagueOptions);
        $stats = $this->getStatArray($player, $match);
        foreach ($options as $optionName => $isActive) {
            if ($isActive) {
                $points += $stats[$optionName];
            }
        }

        return $points;
    }

    /**
     * @param LeagueOptions $leagueOptions
     *
     * @return int[]
     */
    private function buildOptionsArray(LeagueOptions $leagueOptions)
    {
        $optionsArray = [];
        $optionsArray['points'] = $leagueOptions->getDoCountPoints();
        $optionsArray['rebounds'] = $leagueOptions->getDoCountRebounds();
        $optionsArray['assists'] = $leagueOptions->getDoCountAssists();
        $optionsArray['blocks'] = $leagueOptions->getDoCountBlocks();
        $optionsArray['steals'] = $leagueOptions->getDoCountSteals();

        return $optionsArray;
    }

    /**
     * @param Player $player
     * @param Match $match
     *
     * @return int[]
     */
    private function getStatArray(Player $player, Match $match)
    {
        return $this->statisticsRepository->getStatsArray($player, $match);
    }
}