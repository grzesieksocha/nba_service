<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Match;
use AppBundle\Entity\Player;
use AppBundle\Entity\Statistics;
use AppBundle\Helpers\DateHelper;

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
     * @return Statistics[]
     *
     * @throws Exception
     */
    public function getDailyLeaderInStat(DateTime $date, $statistic)
    {
        if (false === property_exists(Statistics::class, $statistic)) {
            throw new Exception(ucfirst($statistic) . ' not found in Statistic class.');
        }

        list($startOfDay, $endOfDay) = DateHelper::getTodayBordersFromEstToCet($date);
        $qb2 = $this->_em->createQueryBuilder();

        $internalQuery =
            $qb2->select('MAX(s2.' . $statistic . ')')
                ->from('AppBundle:Statistics', 's2')
                ->leftJoin('s2.match', 'm2')
                ->andWhere('m2.date > :date')
                ->andWhere('m2.date <= :tomorrow')
                ->setParameter('date', $startOfDay)
                ->setParameter('tomorrow', $endOfDay)
                ->getDQL();

        $qb = $this->createQueryBuilder('s');
        $query = $qb
            ->leftJoin('s.match', 'm')
            ->andWhere('m.date > :date')
            ->andWhere('m.date <= :tomorrow')
            ->andWhere(
                $qb2->expr()->in(
                    's.' . $statistic,
                    $internalQuery
                )
            )
            ->setParameter('date', $startOfDay)
            ->setParameter('tomorrow', $endOfDay)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param DateTime $date
     * @return Statistics
     */
    public function getDailyLeaderSum(DateTime $date)
    {
        list($startOfDay, $endOfDay) = DateHelper::getTodayBordersFromEstToCet($date);

        return $this->createQueryBuilder('s')
            ->select('p.firstName, p.lastName, s.points + s.rebounds + s.assists AS total')
            ->leftJoin('s.match', 'm')
            ->leftJoin('s.player', 'p')
            ->andWhere('m.date > :date')
            ->andWhere('m.date <= :tomorrow')
            ->setParameter('date', $startOfDay)
            ->setParameter('tomorrow', $endOfDay)
            ->addOrderBy('total', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get player statistics from the match and puts them into an array
     *
     * @param Player $player
     * @param Match $match
     *
     * @return  int[]
     */
    public function getStatsArray(Player $player, Match $match)
    {
        $stat = $this->getStats($player, $match);
        return [
            'points' => $stat->getPoints(),
            'rebounds' => $stat->getRebounds(),
            'assists' => $stat->getAssists(),
            'steals' => $stat->getSteals(),
            'blocks' => $stat->getBlocks()
        ];
    }
}
