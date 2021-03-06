<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\League;
use AppBundle\Entity\Match;
use AppBundle\Entity\Pick;
use AppBundle\Entity\Player;
use AppBundle\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PickRepository
 * @package AppBundle\Repository
 */
class PickRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param League $league
     *
     * @return Pick[]
     */
    public function getPicksForUserLeague(User $user, League $league)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $result = $qb->select('p')->from('Pick', 'p')
            ->andWhere('p.user = ?user')
            ->andWhere('p.league = ?league')
            ->orderBy('p.match.date', 'DESC')
            ->setParameters(['user' => $user, 'league' => $league])
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @param ArrayCollection|Player[] $players
     * @param Match $match
     * @param League $league
     * @param User $user
     *
     * @return Player[]|ArrayCollection
     */
    public function removeUsedPicks(ArrayCollection $players, Match $match, League $league, User $user)
    {
        if (!$players->isEmpty()) {
            $qb = $this->createQueryBuilder('p');
            $duplicates = $qb
                ->where($qb->expr()->notIn('p.player', $players->toArray()))
                ->andWhere('p.isActive = 1')
                ->andWhere('p.match = :match')
                ->andWhere('p.league = :league')
                ->andWhere('p.user = :user')
                ->setParameters([
                    'match' => $match,
                    'league' => $league,
                    'user' => $user
                ])->getQuery();

            /** @var Pick $duplicatedPick */
            foreach ($duplicates->getResult() as $duplicatedPick) {
                $player = $duplicatedPick->getPlayer();
                $players->removeElement($player);
            }
        }
        return $players;
    }

    /**
     * @param League $league
     * @param User $user
     *
     * @return Pick[]
     */
    public function findAllByLeagueAndUser(League $league, User $user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.league = :league')
            ->andWhere('p.user = :user')
            ->andWhere('p.isActive = true')
            ->setParameters([
                'league' => $league,
                'user' => $user
            ])->getQuery()->getResult();
    }

    /**
     * @param Match $match
     *
     * @return Pick[]
     */
    public function getPicksForMatch(Match $match)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true')
            ->andWhere('p.pointsInLeague = false')
            ->andWhere('p.match = :match')
            ->setParameter('match', $match)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $league
     * @param $user
     *
     * @return Pick
     */
    public function getLatestCountedPick($league, $user)
    {
        return $this->getQueryBuilderForPicks($league, $user)
            ->andWhere('p.pointsInLeague = true')
            ->addOrderBy('m.date', 'desc')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $league
     * @param $user
     *
     * @return Pick
     */
    public function getNextPick($league, $user)
    {
        return $this->getQueryBuilderForPicks($league, $user)
            ->andWhere('p.pointsInLeague = false')
            ->addOrderBy('m.date', 'asc')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $league
     * @param $user
     *
     * @return QueryBuilder
     */
    private function getQueryBuilderForPicks($league, $user)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.match', 'm')
            ->andWhere('p.isActive = true')
            ->andWhere('p.league = :league')
            ->andWhere('p.user = :user')
            ->setParameter('league', $league)
            ->setParameter('user', $user)
            ->setMaxResults(1);
    }
}
