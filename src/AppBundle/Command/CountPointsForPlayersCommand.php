<?php declare(strict_types = 1);

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Helpers\DateHelper;
use AppBundle\Pick\PointsResolver;
use AppBundle\Repository\PickRepository;
use AppBundle\Repository\MatchRepository;

/**
 * Class CountPointsForPlayersCommand
 * @package AppBundle\Command
 */
class CountPointsForPlayersCommand extends ContainerAwareCommand
{
    /** @var MatchRepository $matchRepo */
    private $matchRepository;
    /** @var PickRepository $pickRepository */
    private $pickRepository;
    /** @var PointsResolver $pointsResolver */
    private $pointsResolver;
    /** @var ObjectManager $entityManager */
    private $entityManager;

    protected function configure()
    {
        $this->setName('nba:count:points')
            ->setDescription('After a long night of games give players their points')
            ->addArgument('date', InputArgument::REQUIRED, 'Date of the matches (YYYY/MM/DD)');
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
        $this->setRepositories();
        $output->writeln([
            'Updating points from ' . $input->getArgument('date') . ' in the database...',
            '---------------------------------',
            ''
        ]);

        $matches = $this->matchRepository->getAllMatchesForDate(
            DateHelper::getEstDateTimeFromString($input->getArgument('date'), 'EST')
        );

        foreach ($matches as $match) {
            $output->writeln('Updating matches for: ' . $match->getDescription());
            $picks = $this->pickRepository->getPicksForMatch($match);
            foreach ($picks as $pick) {
                $leagueHasUser = $this->pointsResolver->updatePoints($pick);
                $this->entityManager->persist($pick);
                $this->entityManager->persist($leagueHasUser);
            }
            $this->entityManager->flush();

        }
    }

    private function setRepositories()
    {
        $this->matchRepository = $this->getContainer()->get('repository.match');
        $this->pickRepository = $this->getContainer()->get('repository.pick');
        $this->pointsResolver = $this->getContainer()->get('app.pick.points_resolver');
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }
}