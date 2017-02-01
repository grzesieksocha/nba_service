<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\League;
use AppBundle\Entity\Pick;
use AppBundle\Entity\User;

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
}
