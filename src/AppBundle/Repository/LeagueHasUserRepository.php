<?php

namespace AppBundle\Repository;

use AppBundle\Entity\LeagueHasUser;
use AppBundle\Entity\User;

use Doctrine\ORM\EntityRepository;

/**
 * LeagueHasUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LeagueHasUserRepository extends EntityRepository
{
    public function getLeaguesForUser(User $user)
    {
        $leagues = [];
        $leaguesHasUser = $this->_em->createQueryBuilder()
            ->select('lhp')
            ->from('AppBundle:LeagueHasUser', 'lhp')
            ->andWhere('lhp.user = :user')
            ->andWhere('lhp.isActive = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        /** @var LeagueHasUser $leagueHasUser */
        foreach ($leaguesHasUser as $leagueHasUser) {
            $leagues[] = $leagueHasUser->getLeague();
        }

        return $leagues;
    }
}