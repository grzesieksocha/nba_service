<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\Match;
use AppBundle\Entity\Player;

use AppBundle\Entity\Statistics;
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

    /**
     * @param Player $player
     * @param Match $match
     *
     * @return Statistics|null
     */
    public function getStats(Player $player, Match $match)
    {
        $query = $this->createQueryBuilder('s')
            ->andWhere('s.match = :match')
            ->andWhere('s.player = :player')
            ->setParameters([
                'match' => $match,
                'player' => $player
            ])
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
