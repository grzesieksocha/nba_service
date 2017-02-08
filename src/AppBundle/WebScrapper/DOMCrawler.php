<?php declare(strict_types = 1);

namespace AppBundle\WebScrapper;

use AppBundle\WebScrapper\Interfaces\HtmlGetter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class DOMCrawler
 * @package AppBundle\WebScrapper
 */
class DOMCrawler
{
    /**
     * @var HTMLGetter
     */
    private $htmlGetter;
    /**
     * @var Crawler
     */
    private $crawler;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param HTMLGetter $htmlGetter
     * @param Crawler $crawler
     * @param string $path
     */
    public function __construct(ContainerInterface $container, HtmlGetter $htmlGetter, Crawler $crawler, string $path)
    {
        $this->container = $container;
        $this->htmlGetter = $htmlGetter;
        $this->crawler = $crawler;
        $content = $this->htmlGetter->getHtmlFromPath($path);
        $this->crawler->addHtmlContent($content);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getData(string $path)
    {
        $playerStats = $this->crawler->filter('th[data-stat="player"]');
        $playerStatsTr = $this->crawler->filter('tr > th[data-stat="player"]');
        $playerStatsMp = $this->crawler->filter('tr > td[data-stat="mp"]');

        $result = [];
        $result = [];
        foreach ($playerStats as $domElement) {
            $result[] = $domElement->nodeValue;
        }
        return $result;
    }

    /**
     * @return string
     */
    public function writeMatchesDataToFile()
    {
        $tableRows = $this->crawler->filter('tr');
        $fileManager = $this->container->get('app.helper.file');
        $date = new \DateTime();
        $fileManager->createFile('matches_' . $date->format('dHis') . '.txt');

        foreach ($tableRows as $domElement) {
            $fileManager->writeToFile($domElement->textContent . PHP_EOL);
        }

        return $fileManager->getFileName();
    }
}