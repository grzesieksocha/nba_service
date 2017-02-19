<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Match;
use AppBundle\Entity\Player;
use AppBundle\Entity\Statistics;

use \DateTime;
use \Exception;

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

    /**
     * @param DateTime $date
     * @param string $statistic
     *
     * @return Statistics
     *
     * @throws Exception
     */
    public function getDailyLeaderInStat(DateTime $date, $statistic)
    {
        if (false === property_exists(Statistics::class, $statistic)) {
            throw new Exception(ucfirst($statistic) . ' not found in Statistic class.');
        }
        $date->setTime(0, 0);
        $endOfDay = clone $date;
        $endOfDay->setTime(23, 59, 59);

        return $this->createQueryBuilder('s')
            ->leftJoin('s.match', 'm')
            ->andWhere('m.date > :date')
            ->andWhere('m.date <= :tomorrow')
            ->setParameter('date', $date)
            ->setParameter('tomorrow', $endOfDay)
            ->addOrderBy('s.' . $statistic, 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param DateTime $date
     * @return Statistics
     */
    public function getDailyLeaderSum(DateTime $date)
    {
        $date->setTime(0, 0);
        $endOfDay = clone $date;
        $endOfDay->setTime(23, 59, 59);

        return $this->createQueryBuilder('s')
            ->select('p.firstName, p.lastName, s.points + s.rebounds + s.assists AS total')
            ->leftJoin('s.match', 'm')
            ->leftJoin('s.player', 'p')
            ->andWhere('m.date > :date')
            ->andWhere('m.date <= :tomorrow')
            ->setParameter('date', $date)
            ->setParameter('tomorrow', $endOfDay)
            ->addOrderBy('total', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
