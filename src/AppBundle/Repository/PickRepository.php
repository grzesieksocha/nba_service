<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\League;
use AppBundle\Entity\Match;
use AppBundle\Entity\Pick;
use AppBundle\Entity\Player;
use AppBundle\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

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
}
