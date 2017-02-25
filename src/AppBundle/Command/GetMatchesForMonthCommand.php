<?php declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Config\Config;
use AppBundle\Repository\MatchRepository;
use AppBundle\WebScrapper\DOMCrawler;
use AppBundle\WebScrapper\UrlGetter;

use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

use \DateTime;

/**
 * Class GetMatchesForMonthCommand
 * @package AppBundle\Command
 */
class GetMatchesForMonthCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('get:matches')
            ->setDescription('Get matches (with scores if available) for given month and upload to the database.')
            ->setHelp('Scrap matches from the http://www.basketball-reference.com/leagues/ site')
            ->addArgument('month', InputArgument::REQUIRED, 'Month of the matches (01-12)');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws FileNotFoundException
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timezone = new DateTimeZone('EST');
        $dateTimeObject = new DateTime('now', $timezone);
        $dateTimeObject->setDate(2017, (int)$input->getArgument('month'), 1);

        $output->writeln([
            'Updating matches from ' . $dateTimeObject->format('F') . ' in the database...',
            '---------------------------------',
            ''
        ]);

        $url = Config::BASKETBALL_REFERENCE . Config::MATCHES_MONTH . strtolower($dateTimeObject->format('F')) . Config::HTML;

        $matchesDataFile = $this->getDomCrawler($url)->writeMatchesDataToFile('matches' . $dateTimeObject->format('Y_m_d'));
        $fileWithData = fopen($matchesDataFile, 'r');

        if (false === $fileWithData) {
            throw new FileNotFoundException('File with matches data not found');
        } else {
            while (!feof($fileWithData)) {
                $line = fgets($fileWithData);
                if(false !== $line && mb_ereg_match('\w{3},\s\w{3}\s\d+', $line)) {
                    $output->writeln('[Processing] ' . $line);
                    $this->processMatchLineAndSave($line, $dateTimeObject);
                }
            }
        };

        $output->writeln([
            '---------------------------------',
            'ENJOY!'
        ]);
    }

    /**
     * @param string $line
     * @param DateTime $date
     *
     * @return void
     */
    private function processMatchLineAndSave(string $line, DateTime $date) {
        $timezone = new DateTimeZone('EST');
        $dateToSet = new DateTime('now', $timezone);

        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->getContainer()->get('repository.match');
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($weekDay, $day, $matchDetails) = explode(',' , $line);
        $day = trim($day);
        $matchDetails = trim($matchDetails);

        $day = substr($day, 4, strlen($day) - 4);
        list($time, $teams) = explode(' pm', $matchDetails);
        $time = substr(trim($time), 4, strlen(trim($time)) - 1);
        $teams = trim($teams);

        if (false !== strpos($teams, 'Box Score')) {
            if (false === strpos($teams, 'OT')) {
                $teams = substr($teams, 0, strlen($teams) - 9);
            } else {
                $teams = substr($teams, 0, strlen($teams) - 11);
            }
        }

        $dateToSet->setDate((int)$date->format('Y'), (int)$date->format('m'), (int)$day);
        list($hour, $minutes) = explode(':', $time);
        $dateToSet->setTime((int)$hour + 12, (int)$minutes);
        $dataBaseTimezone = new DateTimeZone('UTC');
        $dateToSet->setTimezone($dataBaseTimezone);
        $matchRepo->saveMatchFromCommand($teams, $dateToSet);
    }

    /**
     * @param string $url
     *
     * @return DOMCrawler
     */
    private function getDomCrawler(string $url): DOMCrawler
    {
        $getter = new UrlGetter();
        $crawler = new Crawler();
        return new DOMCrawler($this->getContainer(), $getter, $crawler, $url);
    }
}