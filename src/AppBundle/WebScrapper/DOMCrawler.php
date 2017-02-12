<?php declare(strict_types = 1);

namespace AppBundle\WebScrapper;

use AppBundle\Service\FileHelper;
use AppBundle\WebScrapper\Interfaces\HtmlGetter;
use DOMElement;
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
     * @return DOMElement[][]
     */
    public function getStatsData()
    {
        $data = [];
        $tableRows = $this->crawler->filter('tr');
        foreach ($tableRows as $id => $row) {
            $crawler = new Crawler($row);
            $rows = $crawler->children();
            /** @var DOMElement $childRow */
            foreach ($rows as $childRow) {
                if (null !== $childRow && null !== $row) {
                    $data[$id][$row->tagName][] = $childRow->textContent;
                }
            }
        }

        return $data;
    }

    /**
     * @return DOMElement[]
     */
    public function getMatchesData()
    {
        $data = [];
        $tableRows = $this->crawler->filter('tr');

        /** @var DOMElement $domElement */
        foreach ($tableRows as $domElement) {
            $data[] = $domElement->textContent;
        }
        return $data;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function writeMatchesDataToFile(string $fileName)
    {
        $tableWithData = $this->getMatchesData();
        return $this->writeDataToFile($fileName, $tableWithData);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function writeStatsDataToFile(string $fileName)
    {
        $tableWithData = $this->getStatsData();
        return $this->writeDataToFile($fileName, $tableWithData);
    }

    /**
     * @param string $fileName
     * @param array $tableWithData
     *
     * @return string
     */
    public function writeDataToFile(string $fileName, array $tableWithData)
    {
        $fileManager = $this->createFile($fileName);

        foreach ($tableWithData as $domElement) {
            if (null !== $domElement) {
                if (is_array($domElement)) {
                    foreach ($domElement as $id => $element) {
                        foreach ($element as $value) {
                            $fileManager->writeToFile($value . ';');
                        }
                        $fileManager->writeToFile(PHP_EOL);
                    }
                } else {
                    $fileManager->writeToFile($domElement . PHP_EOL);
                }
            }
        }

        return $fileManager->getFileName();
    }

    /**
     * @param string $fileName
     *
     * @return FileHelper
     */
    private function createFile(string $fileName)
    {
        /** @var FileHelper $fileManager */
        $fileManager = $this->container->get('app.helper.file');
        $date = new \DateTime();
        $fileManager->createFile($fileName . '_' . $date->format('dHis') . '.txt');
        return $fileManager;
    }
}