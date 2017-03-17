<?php declare(strict_types = 1);

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Pick\PointsResolver;
use AppBundle\Repository\PickRepository;
use AppBundle\Repository\MatchRepository;

use \DateTime;
use \DateTimeZone;

/**
 * Class CountPointsForPlayersCommand
 * @package AppBundle\Command
 */
class CountPointsForPlayersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('count:points')
            ->setDescription('After a long night of games give players their points')
            ->addArgument('date', InputArgument::REQUIRED, 'Date of the matches (MM/DD)');
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
        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->getContainer()->get('repository.match');
        /** @var PickRepository $pickRepo */
        $pickRepo = $this->getContainer()->get('repository.pick');
        /** @var PointsResolver $pointsResolver */
        $pointsResolver = $this->getContainer()->get('app.pick.points_resolver');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln([
            'Updating points from ' . $input->getArgument('date') . ' in the database...',
            '---------------------------------',
            ''
        ]);

        $date = explode('/', $input->getArgument('date'));
        $timezone = new DateTimeZone('EST');
        $dateTime = new DateTime('now', $timezone);
        $dateTime->setDate((int)$dateTime->format('Y'), (int)$date[0], (int)$date[1]);
        $dateTime->setTime(0, 0);
        $matches = $matchRepo->getAllMatchesForDate($dateTime);

        foreach ($matches as $match) {
            $output->writeln('Update\'ing matches for: ' . $match->getAwayTeam() . ' @ ' . $match->getHomeTeam());
            $picks = $pickRepo->getPicksForMatch($match);
            foreach ($picks as $pick) {
                $pointsResolver->updatePoints($pick);
                $em->flush();
            }
        }
    }
}