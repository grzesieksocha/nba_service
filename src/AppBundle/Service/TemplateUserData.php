<?php

namespace AppBundle\Service;

use AppBundle\Entity\League;
use AppBundle\Repository\LeagueHasUserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TemplateUserData
{
    /** @var  TokenStorage */
    private $tokenStorage;
    /** @var  LeagueHasUserRepository */
    private $leagueHasUserRepository;

    public function __construct($tokenStorage, $leagueHasUserRepository)
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

    private function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}