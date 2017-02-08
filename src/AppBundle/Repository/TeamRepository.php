<?php declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\Team;
use Doctrine\ORM\EntityRepository;

/**
 * Class TeamRepository
 * @package AppBundle\Repository
 */
class TeamRepository extends EntityRepository
{
    /**
     * @return Team[]
     */
    public function getAllTeamsLastNames()
    {
        $teams = $this->findAll();
        $teamsData = [];
        /** @var Team $team */
        foreach ($teams as $team) {
            $teamsData[$team->getLastName()] = $team;
        }
        return $teamsData;
    }
}
