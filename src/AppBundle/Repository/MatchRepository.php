<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Match;
use DateTime;
use Doctrine\ORM\EntityRepository;

class MatchRepository extends EntityRepository
{
    public function getFormattedDatesForFutureMatches()
    {
        $dates = $this->getDatesForFutureMatches();
        $result = [];
        foreach ($dates as $index => $date) {
            /** @var DateTime $date */
            $date = $date['date'];
            $result[$date->format('m/d H:i')] = $date;
        }

        return $result;
    }

    public function getDatesForFutureMatches()
    {
        $now = new DateTime();

        $query = $this->_em->createQueryBuilder()
            ->select('match.date')
            ->from(Match::class, 'match')
            ->andWhere('match.date > :date')
            ->setParameter('date', $now);

        $dates = $query->getQuery()->getResult();

        return $dates;
    }
}
