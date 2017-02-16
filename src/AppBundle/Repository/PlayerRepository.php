<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\League;
use AppBundle\Entity\Match;
use AppBundle\Entity\Player;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class PlayerRepository
 * @package AppBundle\Repository
 */
class PlayerRepository extends EntityRepository
{
    /**
     * @param string[] $player Name and Surname
     * @param Team $team
     * @return Player|null
     */
    public function getPlayerByNameSurnameTeam($player, $team)
    {
        return $this->_em->createQueryBuilder()
            ->select('player')
            ->from(Player::class, 'player')
            ->andWhere('player.firstName = :name')
            ->andWhere('player.lastName = :surname')
            ->andWhere('player.team = :team')
            ->andWhere('player.isActive = ' . Player::V_ACTIVE)
            ->setParameters([
                'name' => $player[0],
                'surname' => $player[1],
                'team' => $team
            ])->getQuery()->getOneOrNullResult();
    }

    public function getAvailablePlayers()
    {
    }
}
