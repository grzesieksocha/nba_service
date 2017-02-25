<?php declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\DataImporter\TeamsDataImporter;
use AppBundle\DataImporter\TeamsSaver;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateTeamsDataCommand
 * @package AppBundle\Command
 */
class UpdateTeamsDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('get:teams')
            ->setDescription('Update teams data in the database.')
            ->setHelp('This command uses erikberg.com api to download basic NBA teams data');
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
        $output->writeln([
            'Updating teams in the database...',
            '---------------------------------',
            ''
        ]);

        $doctrine = $this->getContainer()->get('doctrine');
        $teamsData = new TeamsDataImporter();
        $saver = new TeamsSaver($doctrine);
        $teams = $teamsData->getTeams();
        $saver->saveTeams($teams);

        foreach ($teams as $team) {
            $output->writeln($team['first_name'] . ' ' . $team['last_name'] . ' updated');
        }
    }
}