<?php declare(strict_types = 1);

namespace AppBundle\Service;

use AppBundle\Entity\League;
use AppBundle\Repository\LeagueHasUserRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ActiveLeaguesProvider
 *
 * @package AppBundle\Service
 */
class ActiveLeaguesProvider
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var LeagueHasUserRepository
     */
    private $leagueHasUserRepository;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param ObjectRepository $leagueHasUserRepository
     */
    public function __construct(TokenStorageInterface $tokenStorage, ObjectRepository $leagueHasUserRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->leagueHasUserRepository = $leagueHasUserRepository;
    }

    /**
     * @return League[]
     */
    public function getLeaguesForUser()
    {
        return $this->leagueHasUserRepository->getLeaguesForUser(UserGetter::getUserFromToken($this->tokenStorage));
    }
}