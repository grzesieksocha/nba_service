<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
        /** @var User $user */
        $user = $this->getUser();
        $session = $request->getSession();
        $lastUsernameKey = Security::LAST_USERNAME;
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);
        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        #TODO aaaaa, fake data!
        return [
            'last_username' => $lastUsername,
            'csrf_token' => $csrfToken,
            'dailyLeaders' => [
                ['name' => 'Points', 'player' => 'LeBaron', 'value' => 34]
            ],
            'todayMatches' => [
                ['time' => '19:30', 'awayTeam' => 'OKC', 'homeTeam' => 'MIA', 'awayTeamPoints' => 0, 'homeTeamPoints' => 0],
                ['time' => '19:30', 'awayTeam' => 'OKC', 'homeTeam' => 'MIA', 'awayTeamPoints' => 0, 'homeTeamPoints' => 0]
            ],
            'tommorowMatches' => [
                ['time' => '19:30', 'awayTeam' => 'OKC', 'homeTeam' => 'MIA', 'awayTeamPoints' => 0, 'homeTeamPoints' => 0],
                ['time' => '19:30', 'awayTeam' => 'OKC', 'homeTeam' => 'MIA', 'awayTeamPoints' => 0, 'homeTeamPoints' => 0],
                ['time' => '19:30', 'awayTeam' => 'OKC', 'homeTeam' => 'MIA', 'awayTeamPoints' => 0, 'homeTeamPoints' => 0]
            ]
        ];
    }
}
