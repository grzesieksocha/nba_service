<?php declare(strict_types=1);

namespace AppBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Doctrine\Common\Persistence\ObjectRepository;

use AppBundle\Entity\League;
use AppBundle\Entity\Player;
use AppBundle\Entity\User;
use AppBundle\Repository\PickRepository;

use \DateTime;

/**
 * Class PlayerLeaguePicksProvider
 * @package AppBundle\Service
 */
class PlayerLeaguePicksProvider
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var ActiveLeaguesProvider
     */
    private $activeLeaguesProvider;
    /**
     * @var PickRepository
     */
    private $pickRepository;

    /**
     * @param TokenStorage $tokenStorage
     * @param ActiveLeaguesProvider $activeLeaguesProvider
     * @param ObjectRepository $pickRepository
     */
    public function __construct(TokenStorage $tokenStorage, ActiveLeaguesProvider $activeLeaguesProvider, ObjectRepository $pickRepository)
    {
        $this->activeLeaguesProvider = $activeLeaguesProvider;
        $this->pickRepository = $pickRepository;
        $this->tokenStorage = $tokenStorage[0];
    }

    /**
     * @param Player $player
     * @param League $league
     * @param DateTime $date
     *
     * @return array
     */
    public function exportPickForDate(Player $player, DateTime $date, League $league = null)
    {
        $result = [];
        if (null === $league) {
            $result[$league->getName()] = $this->pickRepository->getPicksForUserLeague($this->getUser(), $league);
        }
        foreach ($this->getLeaguesForUser() as $league) {

        }
        return $result;
    }

    /**
     * @return League[]
     */
    public function getLeaguesForUser()
    {
        return $this->activeLeaguesProvider->getLeaguesForUser();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}