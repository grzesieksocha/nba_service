<?php declare(strict_types = 1);

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeterminePositionsCommand
 * @package AppBundle\Command
 */
class DeterminePositionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('get:scores')
            ->setDescription('Get scores from given date and upload to the database.')
            ->setHelp('Scrap scores from the')
            ->addArgument('date', InputArgument::REQUIRED, 'Date of the matches (YYYY/MM/DD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}