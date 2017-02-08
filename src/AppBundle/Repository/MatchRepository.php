<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\Match;

use Doctrine\ORM\EntityRepository;

use \DateTime;

/**
 * Class MatchRepository
 * @package AppBundle\Repository
 */
class MatchRepository extends EntityRepository
{
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
     * @param DateTime $date
     *
     * @return Match[]
     */
    public function getAllMatchesForDate(DateTime $date)
    {
        $tomorrow = clone $date;
        $tomorrow->modify('+1 day');

        $query = $this->_em->createQueryBuilder()
            ->select('match')
            ->from(Match::class, 'match')
            ->andWhere('match.date >= :date')
            ->andWhere('match.date < :tomorrow')
            ->setParameter('date', $date)
            ->setParameter('tomorrow', $tomorrow)
            ->addOrderBy('match.date', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * @return array
     */
    private function getDatesForFutureMatches()
    {
        $now = new DateTime();

        $query = $this->_em->createQueryBuilder()
            ->select('match.date')
            ->from(Match::class, 'match')
            ->andWhere('match.date > :date')
            ->setParameter('date', $now);

        return $query->getQuery()->getResult();
    }

    private function saveMatch($awayTeamData, $homeTeamData, $date) {
        $match = $this->checkIfMatchExist($awayTeamData, $homeTeamData, $date);
        if ($match) {
            $this->updateMatchScore($match, $awayTeamData, $homeTeamData);
        } else {
            $this->createNewMatch($awayTeamData, $homeTeamData, $date);
        }
    }

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

    private function createNewMatch($awayTeamData, $homeTeamData, $date)
    {
        $match = new Match();
        $match->setAwayTeam($awayTeamData['entity'])
            ->setAwayTeamPoints($awayTeamData['score'])
            ->setHomeTeam($homeTeamData['entity'])
            ->setHomeTeamPoints($homeTeamData['score'])
            ->setDate($date)
            ->setIsActive(Match::V_ACTIVE);

        $em = $this->getEntityManager();
        $em->persist($match);
        $em->flush();
    }

    private function updateMatchScore(Match $match, $awayTeamData, $homeTeamData) {
        $match->setAwayTeamPoints($awayTeamData['score']);
        $match->setHomeTeamPoints($homeTeamData['score']);

        $em = $this->getEntityManager();
        $em->persist($match);
        $em->flush();
    }
}
