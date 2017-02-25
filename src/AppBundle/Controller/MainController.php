<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use AppBundle\Entity\Statistics;
use AppBundle\Repository\MatchRepository;
use AppBundle\Repository\StatisticsRepository;

use \DateTime;
use \DateTimeZone;

/**
 * Class MainController
 * @package AppBundle\Controller
 */
class MainController extends Controller
{
    /** @var MatchRepository */
    private $matchRepository;
    /** @var StatisticsRepository */
    private $statsRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        var_dump($this->container);
    }

    /**
     * @Route("/", name="homepage")
     * @Template("@App/Main/main.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $this->matchRepository = $this->get('repository.match');
        $this->statsRepository = $this->get('repository.statistics');

        list($now, $tommorow, $yesterday) = $this->getDates();

        $dailyLeaders = $this->getDailyLeaders($yesterday);
        $dailyLeader = $this->statsRepository->getDailyLeaderSum($yesterday);
        $matchesToday = $this->matchRepository->getAllMatchesForDate($now);
        $matchesTommorow = $this->matchRepository->getAllMatchesForDate($tommorow);

        return [
            'last_username' => $this->getLastUsername($request),
            'csrf_token' => $this->getCsrfToken(),
            'dailyLeaders' => $dailyLeaders,
            'dailyLeader' => $dailyLeader,
            'matchesToday' => $matchesToday,
            'matchesTommorow' => $matchesTommorow,
            'today' => $now->format('j F'),
            'tommorow' => $tommorow->format('j F'),
            'yesterday' => $yesterday->format('j F')
        ];
    }

    /**
     * @return DateTime[]
     */
    private function getDates(): array
    {
        $timezone = new DateTimeZone('EST');
        $now = new DateTime('now', $timezone);
        $tommorow = clone $now;
        $tommorow->modify('+1 day');
        $yesterday = clone $now;
        $yesterday->modify('-1 day');
        return [$now, $tommorow, $yesterday];
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getLastUsername(Request $request)
    {
        $session = $request->getSession();
        $lastUsernameKey = Security::LAST_USERNAME;
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);
        return $lastUsername;
    }

    /**
     * @return string
     */
    private function getCsrfToken()
    {
        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return $csrfToken;
    }

    /**
     * @param DateTime $yesterday
     * @return array
     */
    private function getDailyLeaders(DateTime $yesterday): array
    {
        $dailyLeaders = [];
        foreach ($this->getStatisticsArray() as $stat) {
            $checkDate = clone $yesterday;
            $statsEntities = $this->statsRepository->getDailyLeaderInStat($checkDate, $stat);
            if (null !== $statsEntities) {
                /** @var Statistics $statsEntity */
                foreach ($statsEntities as $statsEntity) {
                    $dailyLeaders[$stat][$statsEntity->getId()]['player'] =
                        $statsEntity->getPlayer()->getFirstName() . ' ' . $statsEntity->getPlayer()->getLastName();
                    $dailyLeaders[$stat][$statsEntity->getId()]['value'] = $statsEntity->{'get' . $stat}();
                }
            }
        }
        return $dailyLeaders;
    }

    /**
     * @return string[] Statistics properties to show on the main page
     */
    private function getStatisticsArray()
    {
        return ['points', 'rebounds', 'assists', 'blocks', 'steals'];
    }
}
