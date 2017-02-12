<?php declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Config\Config;
use AppBundle\Entity\Match;
use AppBundle\Entity\Player;
use AppBundle\Entity\Statistics;
use AppBundle\Entity\Team;
use AppBundle\Repository\MatchRepository;
use AppBundle\Repository\PlayerRepository;
use AppBundle\Repository\StatisticsRepository;
use AppBundle\Service\BasketReferenceDataGetter;
use AppBundle\Service\FileHelper;
use AppBundle\WebScrapper\DOMCrawler;
use AppBundle\WebScrapper\UrlGetter;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

use \DateTime;

/**
 * Class GetScoresFromDate
 * @package AppBundle\Command
 */
class GetScoresFromDateCommand extends ContainerAwareCommand
{
    /** @var PlayerRepository $playerRepo */
    private $playerRepo;
    /** @var StatisticsRepository $statsRepo */
    private $statsRepo;
    /** @var BasketReferenceDataGetter $brRepo */
    private $brRepo;

    protected function configure()
    {
        $this->setName('app:get:scores')
            ->setDescription('Get scores from given date and upload to the database.')
            ->setHelp('Scrap scores from the')
            ->addArgument('date', InputArgument::REQUIRED, 'Date of the matches (YYYY/MM/DD');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->playerRepo = $this->getContainer()->get('repository.player');
        $this->statsRepo = $this->getContainer()->get('repository.statistics');
        $this->brRepo = $this->getContainer()->get('app.getter.basketball_reference');

        $output->writeln([
            'Updating scores from ' . $input->getArgument('date') . ' in the database...',
            '---------------------------------',
            ''
        ]);

        $date = explode('/', $input->getArgument('date'));
        $dateTime = new DateTime();
        $dateTime->setDate((int)$date[0], (int)$date[1], (int)$date[2]);
        $dateTime->setTime(0, 0);
        $urls = $this->buildUrlMatchArray($dateTime);
        $matchesDataFiles = [];
        foreach ($urls as $url => $match) {
            $filename = 'stats_' . $match->getAwayTeam()->getShort() . '_' . $match->getHomeTeam()->getShort();
            $processedFilename = $this->getDomCrawler($url)->writeStatsDataToFile($filename);
            $matchesDataFiles[$processedFilename] = $match;
            $output->writeln('Processed ' . $match->getAwayTeam()->getShort() . '_' . $match->getHomeTeam()->getShort() . ' & waiting...');
            sleep(rand(30, 90));
            $output->writeln('Processing...');
        }

        foreach ($matchesDataFiles as $matchesDataFile => $match) {
            $fileWithDataName = $this->prepareFile($matchesDataFile, $match);
            $fileWithData = fopen($fileWithDataName, 'r');

            if (false === $fileWithData) {
                throw new FileNotFoundException('File with stats data not found');
            } else {
                $this->processStatFile($fileWithData, $match);
            };
            $output->writeln('Match processed');
        }

        $output->writeln([
            '---------------------------------',
            'ENJOY!'
        ]);

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

    /**
     * @param DateTime $date
     *
     * @return Match[]
     */
    private function buildUrlMatchArray(DateTime $date) {
        $data = [];
        $teams = [];
        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->getContainer()->get('repository.match');
        $matches = $matchRepo->getAllMatchesForDate($date);

        foreach ($matches as $match) {
            $teams[$match->getHomeTeam()->getShort()] = $match;
        }

        foreach ($teams as $team => $match) {
            $team = $team === 'CHA' ? 'CHO' : $team;
            $url = Config::BASKETBALL_REFERENCE .
                Config::BOXSCORES .
                $date->format('Y') . $date->format('m') . $date->format('d') . '0' . $team . Config::HTML;
            $data[$url] = $match;
        }
        return $data;
    }

    /**
     * @param string $fileName
     * @param Match $match
     *
     * @return string
     */
    private function prepareFile(string $fileName, Match $match)
    {
        $fileToProcess = new FileHelper($fileName, '');
        $fileToProcess = $fileToProcess->getFileResource();
        $processedFile = new FileHelper('done_' . substr($fileName, -26, 26));
        $team = 0;
        $process = true;

        $processedFile->writeToFile($match->getAwayTeam()->getShort() . PHP_EOL);
        while (!feof($fileToProcess)) {
            $line = fgets($fileToProcess);
            if(false !== $line ) {
                if(0 === strpos($line, ';Basic') && $team == 1) {
                    $process = true;
                    $processedFile->writeToFile($match->getHomeTeam()->getShort() . PHP_EOL);
                } elseif (0 === strpos($line, ';Advanced') && $process) {
                    #TODO process advanced stats
                    $team++;
                    $process = false;
                } elseif (false === strpos($line, 'Starters') &&
                    false === strpos($line, ';Basic') &&
                    false === strpos($line, 'Team Totals') &&
                    false === strpos($line, ';Did Not Play;') &&
                    false === strpos($line, ';Not With Team;') &&
                    $process
                ) {
                    $processedFile->writeToFile($line);
                }
            }
        }
        return $processedFile->getFileName();
    }

    /**
     * @param resource $fileWithData
     * @param Match $match
     */
    private function processStatFile($fileWithData, $match)
    {
        $team = $match->getAwayTeam();
        $changeTeam = false;
        $starter = true;
        while (!feof($fileWithData)) {
            $line = fgets($fileWithData);
            if(false !== $line) {
                if (1 === preg_match('/^Reserves/', $line)) {
                    $starter = false;
                } elseif (0 === preg_match('/^[A-Z]{3}\n$/', $line)) {
                    $this->processStatLineAndSave($line, $starter, $team, $match);
                    $changeTeam = true;
                } elseif ($changeTeam) {
                    $team = $match->getHomeTeam();
                    $starter = true;
                }
            }
        }
    }

    /**
     * @param string $line
     * @param bool $starter
     * @param Team $team
     * @param Match $match
     */
    private function processStatLineAndSave(string $line, bool $starter, Team $team, Match $match)
    {
        list($player, $minutesPlayed, $fieldGoals, $fieldGoalAttempts, $fieldGoalPercentage,
            $threePoint, $threePointAttempts, $threePointPercentage, $freeThrows, $freeThrowsAttempts,
            $freeThrowsPercentage, $offensiveRebounds, $defensiveRebounds, $totalRebounds, $assists,
            $steals, $blocks, $turnovers, $personalFouls, $points, $plusMinus) = explode(';', $line);

        $player = explode(' ', $player);
        $playerEntity = $this->playerRepo->getPlayerByNameSurnameTeam($player, $team);
        if (null === $playerEntity) {
            $playerEntity = $this->brRepo->createPlayer($player);
            $playerEntity->setTeam($team);
            $this->savePlayer($playerEntity);
        }

        if(null === $this->statsRepo->getStats($playerEntity, $match)) {
            $this->createNewStats($playerEntity, $match, $starter, (int)$points, (int)$totalRebounds, (int)$assists, (int)$blocks, (int)$steals, (int)$turnovers, $minutesPlayed);
        }
    }

    /**
     * @param Player $playerEntity
     */
    private function savePlayer(Player $playerEntity)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($playerEntity);
        $em->flush();
    }

    /** @noinspection PhpTooManyParametersInspection */

    /**
     * @param Player $player
     * @param Match $match
     * @param bool $starter
     * @param int $points
     * @param int $totalRebounds
     * @param int $assists
     * @param int $blocks
     * @param int $steals
     * @param int $turnovers
     * @param string $minutesPlayed
     */
    private function createNewStats(Player $player, Match $match, bool $starter, int $points, int $totalRebounds, int $assists, int $blocks, int $steals, int $turnovers, string $minutesPlayed)
    {
        $statsLine = new Statistics();
        $statsLine->setPlayer($player)->setMatch($match)->setIsStarter($starter)
            ->setPoints($points)->setRebounds($totalRebounds)->setAssists($assists)
            ->setBlocks($blocks)->setSteals($steals)->setTurnovers($turnovers)
            ->setMinutes($minutesPlayed)->setIsActive(true);
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($statsLine);
        $em->flush();
    }
}