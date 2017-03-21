<?php declare(strict_types = 1);

namespace AppBundle\Service;

use AppBundle\Entity\LeagueHasUser;
use AppBundle\Exceptions\InvalidPasswordException;
use AppBundle\Repository\LeagueHasUserRepository;
use AppBundle\Repository\LeagueRepository;
use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\League;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class LeagueJoiner
 * @package AppBundle\Service
 */
class LeagueJoiner
{
    /** @var LeagueRepository|EntityRepository */
    private $leagueRepository;

    /** @var LeagueHasUserRepository|EntityRepository */
    private $leagueHasUserRepository;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param EntityRepository $leagueRepository
     * @param EntityRepository $leagueHasUserRepository
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityRepository $leagueRepository,
        EntityRepository $leagueHasUserRepository
    ) {
        $this->leagueRepository = $leagueRepository;
        $this->tokenStorage = $tokenStorage;
        $this->leagueHasUserRepository = $leagueHasUserRepository;
    }

    /**
     * @param int $leagueId
     * @param string $password
     *
     * @return bool
     */
    public function validateAndJoinLeague(int $leagueId, string $password = null)
    {
        $league = $this->leagueRepository->find($leagueId);
        $joinAllowed = true;
        if (!$league instanceof League) {
            return false;
        }

        #TODO aaaaa, password is not encrypted, you know what to do
        if ($league->getIsPrivate()) {
            $joinAllowed = $league->getPassword() === $password;
            if (false === $joinAllowed) {
                throw new InvalidPasswordException();
            }
        }

        if ($joinAllowed) {
            $this->joinLeague($league);
        }
        return $joinAllowed;
    }

    /**
     * @param $league
     */
    private function joinLeague(League $league)
    {
        $lhu = new LeagueHasUser();
        $lhu->setUser(UserGetter::getUserFromToken($this->tokenStorage))
            ->setLeague($league)
            ->setIsActive(true)
            ->setSumOfPoints(0)
            ->setPosition(0)
            ->setIsLeagueAdmin(false);
        $this->leagueHasUserRepository->save($lhu);
    }
}