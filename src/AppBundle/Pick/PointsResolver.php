<?php declare(strict_types = 1);

namespace AppBundle\Pick;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Pick;
use AppBundle\Entity\Match;
use AppBundle\Entity\Player;
use AppBundle\Entity\LeagueOptions;
use AppBundle\Repository\StatisticsRepository;
use AppBundle\Repository\LeagueHasUserRepository;
use AppBundle\Entity\League;
use AppBundle\Entity\LeagueHasUser;
use AppBundle\Entity\User;

/**
 * Class PointsResolver
 * @package AppBundle\Pick
 */
class PointsResolver
{
    /**
     * @var StatisticsRepository|EntityRepository
     */
    private $statisticsRepository;
    /**
     * @var LeagueHasUserRepository|EntityRepository
     */
    private $leagueHasUserRepository;

    /**
     * @param EntityRepository $statisticsRepository
     * @param EntityRepository $leagueHasUserRepository
     * @internal param MatchRepository $matchRepository
     */
    public function __construct(
        EntityRepository $statisticsRepository,
        EntityRepository $leagueHasUserRepository
) {
        $this->statisticsRepository = $statisticsRepository;
        $this->leagueHasUserRepository = $leagueHasUserRepository;
    }

    /**
     * Counts points that user got from the last pick and adds it to the sum of points in his league
     *
     * @param Pick $pick
     *
     * @return LeagueHasUser to persist it
     */
    public function updatePoints(Pick $pick)
    {
        $league = $pick->getLeague();
        $options = $league->getOptions();
        $points = $this->countPoints($pick->getMatch(), $pick->getPlayer(), $options);
        $lhu = $this->addPointsToUserAccount($league, $pick->getUser(), $points);
        $pick->setPoints($points);
        $pick->setPointsInLeague(true);
        return $lhu;
    }

    /**
     * Counts player points from the last user pick
     *
     * @param Match $match
     * @param Player $player
     * @param LeagueOptions $leagueOptions
     *
     * @return int
     */
    private function countPoints(Match $match, Player $player, LeagueOptions $leagueOptions)
    {
        $points = 0;
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
     * Checks which player stats are taken in the consideration by the league
     *
     * @param LeagueOptions $leagueOptions
     *
     * @return int[] statName => true|false
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
     * Gets an array with the players statistics from a given match
     *
     * @param Player $player
     * @param Match $match
     *
     * @return int[] statName => value
     */
    private function getStatArray(Player $player, Match $match)
    {
        return $this->statisticsRepository->getStatsArray($player, $match);
    }

    /**
     * Adding points from the last pick to the overall count in the league
     *
     * @param League $league
     * @param User $user
     * @param int $points
     *
     * @return LeagueHasUser
     *
     * @throws EntityNotFoundException
     */
    private function addPointsToUserAccount(League $league, User $user, int $points)
    {
        $lhu = $this->leagueHasUserRepository->findOneBy([
            'league' => $league,
            'user' => $user,
            'isActive' => true
        ]);

        if ($lhu instanceof LeagueHasUser) {
            return $lhu->setSumOfPoints($lhu->getSumOfPoints() + $points);
        } else {
            throw new EntityNotFoundException();
        }
    }
}