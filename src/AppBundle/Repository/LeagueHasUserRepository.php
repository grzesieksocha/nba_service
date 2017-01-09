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
        $query =  $this->_em->createQueryBuilder()
            ->select('lhu')
            ->from('AppBundle:LeagueHasUser', 'lhu')
            ->andWhere('lhu.isActive = true');

        if (false === $user->hasRole('ROLE_ADMIN')) {
            $query->andWhere('lhu.user = :user')
                ->setParameter('user', $user);
        }

        $leaguesHasUser = $query->getQuery()->getResult();

        $leagues = [];
        /** @var LeagueHasUser $leagueHasUser */
        foreach ($leaguesHasUser as $leagueHasUser) {
            $leagues[] = $leagueHasUser->getLeague();
        }

        return $leagues;
    }
}
