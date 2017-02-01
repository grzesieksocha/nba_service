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
    protected function getDatesForFutureMatches()
    {
        $now = new DateTime();

        $query = $this->_em->createQueryBuilder()
            ->select('match.date')
            ->from(Match::class, 'match')
            ->andWhere('match.date > :date')
            ->setParameter('date', $now);

        return $query->getQuery()->getResult();
    }
}
