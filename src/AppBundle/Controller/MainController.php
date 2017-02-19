<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\MatchRepository;
use AppBundle\Repository\StatisticsRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * Class MainController
 * @package AppBundle\Controller
 */
class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("@App/Main/main.html.twig")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $now = new DateTime();
        $tommorow = clone $now;
        $tommorow->modify('+1 day');
        $yesterday = clone $now;
        $yesterday->modify('-1 day');
        $session = $request->getSession();
        $lastUsernameKey = Security::LAST_USERNAME;
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);
        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->get('repository.match');
        /** @var StatisticsRepository $statsRepo */
        $statsRepo = $this->get('repository.statistics');
        $dailyLeaders = [];
        foreach ($this->getStatisticsArray() as $stat) {
            $statsEntity = $statsRepo->getDailyLeaderInStat($yesterday, $stat);
            if (null !== $statsEntity) {
                $dailyLeaders[$stat]['player'] =
                    $statsEntity->getPlayer()->getFirstName() . ' ' . $statsEntity->getPlayer()->getLastName();
                $dailyLeaders[$stat]['value'] = $statsEntity->{'get' . $stat}();
            }
        }
        $dailyLeader = $statsRepo->getDailyLeaderSum($yesterday);
        $matchesToday = $matchRepo->getAllMatchesForDate($now);
        $matchesTommorow = $matchRepo->getAllMatchesForDate($tommorow);

        return [
            'last_username' => $lastUsername,
            'csrf_token' => $csrfToken,
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
     * @return string[] Statistics properties to show on the main page
     */
    private function getStatisticsArray()
    {
        return ['points', 'rebounds', 'assists', 'blocks', 'steals'];
    }
}
