<?php declare(strict_types = 1);

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\LeagueHasUser;
use AppBundle\Repository\LeagueRepository;
use AppBundle\Repository\LeagueHasUserRepository;

/**
 * Class DeterminePositionsCommand
 * @package AppBundle\Command
 */
class DeterminePositionsCommand extends ContainerAwareCommand
{
    /** @var ObjectManager $entityManager */
    private $entityManager;
    /** @var LeagueRepository $leagueRepository */
    private $leagueRepository;
    /** @var LeagueHasUserRepository $leagueHasUserRepository */
    private $leagueHasUserRepository;

    protected function configure()
    {
        $this->setName('nba:set:positions')
            ->setDescription('Determine users positions in leagues');
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
            'Updating users positions in the database...',
            '---------------------------------',
            ''
        ]);

        $leagues = $this->leagueRepository->findBy(['isActive' => true]);
        foreach ($leagues as $league) {
            $pointsUserMap = [];
            $lhu = $this->leagueHasUserRepository->findBy(['league' => $league, 'isActive' => true]);
            /** @var LeagueHasUser[] $lhu */
            foreach ($lhu as $userInLeague) {
                $points = $userInLeague->getSumOfPoints();
                if ($points === 0) {
                    $points = 'zero';
                }
                $pointsUserMap[$points][] = $userInLeague;
            }
            krsort($pointsUserMap);
            $position = $positionToSave = 1;
            foreach ($pointsUserMap as $pointsUser) {
                /** @var LeagueHasUser[] $pointsUser */
                foreach ($pointsUser as $user) {
                    $user->setPosition($positionToSave);
                    $this->entityManager->persist($user);
                    $position++;
                }
                $positionToSave = $position;
            }
            $this->entityManager->flush();
        }
    }

    private function setRepositories()
    {
        $this->leagueRepository = $this->getContainer()->get('repository.league');
        $this->leagueHasUserRepository = $this->getContainer()->get('repository.league_has_user');
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}