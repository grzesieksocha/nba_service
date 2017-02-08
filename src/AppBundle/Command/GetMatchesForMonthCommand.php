<?php declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Repository\MatchRepository;
use AppBundle\WebScrapper\DOMCrawler;
use AppBundle\WebScrapper\FileGetter;

use AppBundle\WebScrapper\UrlGetter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class GetMatchesForMonthCommand
 * @package AppBundle\Command
 */
class GetMatchesForMonthCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:get:matches')
            ->setDescription('Get matches (with scores if available) for given month and upload to the database.')
            ->setHelp('Scrap matches from the http://www.basketball-reference.com/leagues/ site')
            ->addArgument('month', InputArgument::REQUIRED, 'Month of the matches (01-12)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Updating matches from ' . $input->getArgument('month') . ' in the database...',
            '---------------------------------',
            ''
        ]);

        $dateTimeObject = new \DateTime();
        $dateTimeObject->setDate(2017, (int)$input->getArgument('month'), 1);
        $url = 'http://www.basketball-reference.com/leagues/NBA_2017_games-' . strtolower($dateTimeObject->format('F')) .'.html';

//        $url = './example_data/matchesData.html';

        $getter = new UrlGetter();
//        $getter = new FileGetter();
        $crawler = new Crawler();
        $domCrawler = new DOMCrawler($this->getContainer(), $getter, $crawler, $url);

        $matchesDataFile = $domCrawler->writeMatchesDataToFile();
        $fileWithData = fopen($matchesDataFile, 'r');
        $arrayWithMatches = [];
        if (false === $fileWithData) {
            throw new FileNotFoundException('File with matches data not found');
        } else {
            while (!feof($fileWithData)) {
                $line = fgets($fileWithData);
                if(false !== $line && mb_ereg_match('\w{3},\s\w{3}\s\d+', $line)) {
                    $line = $this->processMatchLineAndSave($line, $dateTimeObject);
                }
            }
        };

        echo 'DONE!' . PHP_EOL;
    }

    /**
     * @param string $line
     * @param \DateTime $date
     *
     * @return array
     */
    private function processMatchLineAndSave(string $line, \DateTime $date) {
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

        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->getContainer()->get('repository.match');
        $date->setDate((int)$date->format('Y'), (int)$date->format('m'), (int)$day);
        list($hour, $minutes) = explode(':', $time);
        $date->setTime((int)$hour, (int)$minutes);
        $matchRepo->saveMatchFromCommand($teams, $date);
    }
}