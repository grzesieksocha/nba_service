<?php declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\WebScrapper\DOMCrawler;
use AppBundle\WebScrapper\FileGetter;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class GetScoresFromDate
 * @package AppBundle\Command
 */
class GetScoresFromDateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:get:scores')
            ->setDescription('Get scores from given date and upload to the database.')
            ->setHelp('Scrap scores from the')
            ->addArgument('date', InputArgument::REQUIRED, 'Date of the matches (YYYY/MM/DD');
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
            'Updating scores from ' . $input->getArgument('date') . ' in the database...',
            '---------------------------------',
            ''
        ]);

//        $date = explode('/', $input->getArgument('date'));
//        $url = 'http://www.basketball-reference.com/boxscores/' . $date[0] . $date[1] . $date[2] .'0ORL.html';

        $path = './example_data/simpleHtml.html';
//        $scandir = scandir($path);

        $domCrawler = new DOMCrawler(new FileGetter(), new Crawler());
        $data = $domCrawler->getData($path);

        echo 'Hello!';
    }

}