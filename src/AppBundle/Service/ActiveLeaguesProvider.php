<?php declare(strict_types = 1);

namespace AppBundle\Service;

use AppBundle\Entity\League;
use AppBundle\Repository\LeagueHasUserRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class ActiveLeaguesProvider
 *
 * @package AppBundle\Service
 */
class ActiveLeaguesProvider
{
    /**
     * @var  TokenStorage
     */
    private $tokenStorage;
    /**
     * @var LeagueHasUserRepository
     */
    private $leagueHasUserRepository;

    /**
     * @param $tokenStorage
     * @param ObjectRepository $leagueHasUserRepository
     */
    public function __construct($tokenStorage, ObjectRepository $leagueHasUserRepository)
    {
        $this->tokenStorage = $tokenStorage[0];
        $this->leagueHasUserRepository = $leagueHasUserRepository;
    }

    /**
     * @return League[]
     */
    public function getLeaguesForUser()
    {
        return $this->leagueHasUserRepository->getLeaguesForUser($this->getUser());
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}