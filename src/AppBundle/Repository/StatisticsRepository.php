<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\Match;
use AppBundle\Entity\Player;

use Doctrine\ORM\EntityRepository;

/**
 * Class StatisticsRepository
 * @package AppBundle\Repository
 */
class StatisticsRepository extends EntityRepository
{
    /**
     * @param Match $match
     * @return array
     */
    public function getStatsForMatch(Match $match)
    {
        $query = $this->createQueryBuilder('s')
            ->andWhere('s.match = :match')
            ->setParameter('match', $match)
            ->getQuery();

        return $query->getResult();
    }
}
