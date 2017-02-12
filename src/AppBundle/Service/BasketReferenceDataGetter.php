<?php declare(strict_types = 1);

namespace AppBundle\Service;
use AppBundle\Entity\Player;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BasketReferenceDataGetter
 * @package AppBundle\Service
 */
class BasketReferenceDataGetter
{
    const BASKETBALL_REFERENCE = 'http://www.basketball-reference.com/';
    const PLAYER_LINK_END = '01.html';
    const PLAYERS = 'players/';
    const SEPARATOR = '/';

    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string[] $player Name & Surname
     * @return Player
     */
    public function createPlayer(array $player)
    {
        $em = $this->container->get('doctrine')->getManager();
        $playerEntity = new Player();
        $playerEntity->setFirstName($player[0])->setLastName($player[1])->setIsActive(true);
        $em->persist($playerEntity);
        $em->flush();

        return $playerEntity;
        // Below maybe in the future...
//        $url = $this->buildUrlForPlayer($player);
//        $crawler = $this->getDomCrawler($url);
    }

//    /**
//     * @param string[] $player Name & Surname
//     *
//     * @return string
//     */
//    private function buildUrlForPlayer(array $player)
//    {
//        if (strlen($player[1]) <= 5) {
//            $surname = $player[1];
//        } else {
//            $surname = substr($player[1], 0, 5);
//        }
//        $firstLetter = strtolower(substr($surname, 0, 1));
//        $name = substr($player[0], 0, 2);
//        return self::BASKETBALL_REFERENCE . self::PLAYERS . $firstLetter . self::SEPARATOR . $surname . $name . self::PLAYER_LINK_END;
//    }
//
//    /**
//     * @param string $url
//     *
//     * @return DOMCrawler
//     */
//    private function getDomCrawler(string $url): DOMCrawler
//    {
//        $getter = new UrlGetter();
//        $crawler = new Crawler();
//        return new DOMCrawler($this->container, $getter, $crawler, $url);
//    }
}