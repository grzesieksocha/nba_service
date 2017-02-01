<?php declare(strict_types = 1);

namespace AppBundle\Service;

use AppBundle\Repository\MatchRepository;

use Doctrine\Common\Persistence\ObjectRepository;

use \DateTime;

/**
 * Class NextMatchProvider
 * @package AppBundle\Service
 */
class NextMatchProvider
{
    /**
     * @var MatchRepository
     */
    private $matchRepository;

    /**
     * @param ObjectRepository $matchRepository
     */
    public function __construct(ObjectRepository $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    public function getFirstGameToday()
    {
        $today = new DateTime();
        $today->setTime(0, 0);
        $firstMatch = $this->matchRepository->getFirstMatchOfTheDay($today);
    }
}