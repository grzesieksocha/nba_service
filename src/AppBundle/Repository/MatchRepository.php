<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use AppBundle\Helpers\DateHelper;
use AppBundle\Entity\Match;
use AppBundle\Exceptions\SaveEntityFailedException;

use \DateInterval;
use \DateTime;

/**
 * Class MatchRepository
 * @package AppBundle\Repository
 */
class MatchRepository extends EntityRepository
{
    /**
     * @param $teamsWithScores
     * @param $date
     *
     * @return array
     */
    public function saveMatchFromCommand($teamsWithScores, $date)
    {
        /** @var TeamRepository $teamsRepo */
        $teamsRepo = $this->getEntityManager()->getRepository('AppBundle:Team');
        $teams = $teamsRepo->getAllTeamsLastNames();

        $matchDetails = [];
        foreach ($teams as $teamLastName => $team) {
            $teamInMatch = strpos($teamsWithScores, $teamLastName);
            if ($teamInMatch) {
                $score = '';
                $matchDetails[$teamInMatch]['pos'] = $teamInMatch;
                $matchDetails[$teamInMatch]['entity'] = $team;
                $posToCheckForScore = $teamInMatch + strlen($teamLastName);
                if (isset($teamsWithScores[$posToCheckForScore]) &&
                    preg_match('/\d/', $teamsWithScores[$posToCheckForScore])
                ) {
                    while (
                        isset($teamsWithScores[$posToCheckForScore]) &&
                        preg_match('/\d/', $teamsWithScores[$posToCheckForScore])
                    ) {
                        $score .= $teamsWithScores[$posToCheckForScore];
                        $posToCheckForScore++;
                    }
                    $matchDetails[$teamInMatch]['score'] = (int)$score;
                } else {
                    $matchDetails[$teamInMatch]['score'] = 0;
                }
            }
        }
        ksort($matchDetails);
        $awayTeamData = array_shift($matchDetails);
        $homeTeamData = array_shift($matchDetails);
        $this->saveMatch($awayTeamData, $homeTeamData, $date);

        return $matchDetails;
    }

    /**
     * Return dates to pick
     *
     * @return array
     */
    public function getFormattedDatesForFutureMatches()
    {
        $dates = $this->getDatesForFutureMatches();
        $result = [];
        foreach ($dates as $index => $date) {
            /** @var DateTime $date */
            $date = $date['date'];
            $result[$date->format('m/d')] = $date->format('m/d');
        }

        return $result;
    }

    /**
     * @param DateTime $day
     *
     * @return Match
     */
    public function getFirstMatchOfTheDay(DateTime $day)
    {
        return $this->getAllMatchesForDate($day)[0];
    }

    /**
     * Getting all the matches from a given day.
     * If timezone of $date is different than CET then converts it to CET
     *
     * @param DateTime $date
     *
     * @return Match[]
     */
    public function getAllMatchesForDate(DateTime $date)
    {
        if ($date->getTimezone()->getName() !== 'UTC') {
            list($startOfDay, $endOfDay) = DateHelper::getTodayBordersFromEstToCet($date);
        } else {
            list($startOfDay, $endOfDay) = DateHelper::getTodayBorders($date);
        }

        $query = $this->createQueryBuilder('m')
            ->andWhere('m.date > :date')
            ->andWhere('m.date <= :tomorrow')
            ->setParameter('date', $startOfDay)
            ->setParameter('tomorrow', $endOfDay)
            ->addOrderBy('m.date', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * @param DateTime $date
     *
     * @return string[]
     */
    public function getMatchesForStatsCommand(DateTime $date)
    {
        $matches = $this->getAllMatchesForDate($date);
        $result = [];
        foreach ($matches as $match) {
            $result[] = $match->getHomeTeam()->getShort();
        }
        return $result;
    }

    /**
     * @return array
     */
    private function getDatesForFutureMatches()
    {
        $now = new DateTime();

        $query = $this->createQueryBuilder('m')
            ->select('m.date')
            ->andWhere('m.date > :date')
            ->setParameter('date', $now);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $awayTeamData
     * @param $homeTeamData
     * @param $date
     */
    private function saveMatch($awayTeamData, $homeTeamData, $date) {
        $match = $this->checkIfMatchExist($awayTeamData, $homeTeamData, $date);
        if ($match) {
            $this->updateMatchScore($match, $awayTeamData, $homeTeamData);
        } else {
            $this->createNewMatch($awayTeamData, $homeTeamData, $date);
        }
    }

    /**
     * @param $awayTeamData
     * @param $homeTeamData
     * @param $date
     *
     * @return Match|bool
     */
    private function checkIfMatchExist($awayTeamData, $homeTeamData, $date)
    {
        $match = $this->_em->createQueryBuilder()
            ->select('match')
            ->from(Match::class, 'match')
            ->andWhere('match.awayTeam = :awayTeam')
            ->andWhere('match.homeTeam = :homeTeam')
            ->andWhere('match.date = :date')
            ->setParameters([
                'awayTeam' => $awayTeamData['entity'],
                'homeTeam' => $homeTeamData['entity'],
                'date' => $date,
            ])->getQuery()->getResult();

        if (isset($match[0]) && $match[0] instanceof Match) {
            return $match[0];
        }
        return false;
    }

    /**
     * @param $awayTeamData
     * @param $homeTeamData
     * @param $date
     */
    private function createNewMatch($awayTeamData, $homeTeamData, $date)
    {
        $match = new Match();
        $match->setAwayTeam($awayTeamData['entity'])
            ->setAwayTeamPoints($awayTeamData['score'])
            ->setHomeTeam($homeTeamData['entity'])
            ->setHomeTeamPoints($homeTeamData['score'])
            ->setDate($date)
            ->setIsActive(Match::V_ACTIVE);

        $this->save($match);
    }

    /**
     * @param Match $match
     * @param $awayTeamData
     * @param $homeTeamData
     */
    private function updateMatchScore(Match $match, $awayTeamData, $homeTeamData) {
        $match->setAwayTeamPoints($awayTeamData['score']);
        $match->setHomeTeamPoints($homeTeamData['score']);

        $this->save($match);
    }

    /**
     * @param $match
     *
     * @throws SaveEntityFailedException
     */
    private function save($match)
    {
        try {
            $em = $this->getEntityManager();
            $em->persist($match);
            $em->flush();
        } catch (\Exception $e) {
            throw new SaveEntityFailedException($e->getMessage());
        }
    }

    /**
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     *
     * @return Match[]
     */
    public function getAllMatchesForDateRange(DateTime $dateFrom, DateTime $dateTo)
    {
        $dateFrom->modify('-1 day');
        $matches = [];
        $interval = new DateInterval('P1D');
        while ($dateFrom->add($interval) <= $dateTo) {
            $games = $this->getAllMatchesForDate(clone $dateFrom);
            $matches = array_merge($matches, $games);
        }
        return $matches;
    }
}
