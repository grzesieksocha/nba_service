<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\LeagueHasUser;
use AppBundle\Entity\Pick;
use AppBundle\Exceptions\InvalidPasswordException;
use AppBundle\Form\LeagueType;

use AppBundle\Repository\LeagueHasUserRepository;
use AppBundle\Repository\PickRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class LeagueController
 * @package AppBundle\Controller
 * @Route("/league")
 */
class LeagueController extends Controller
{
    /**
     * @Route("/list", name="league_list")
     * @Template("@App/league/leagueList.html.twig")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var LeagueHasUserRepository $leagueHasUserRepo */
        $leagueHasUserRepo = $em->getRepository('AppBundle:LeagueHasUser');
        /** @var PickRepository $pickRepo */
        $pickRepo = $em->getRepository('AppBundle:Pick');
        $leagues = $leagueHasUserRepo->getLeaguesForUser($this->getUser());

        $positions = $this->getPositionsMap($leagues, $leagueHasUserRepo);
        $leaders = $this->getLeadersMap($leagues, $leagueHasUserRepo);
        $lastPicks = $this->getLatestPicksMap($leagues, $pickRepo);
        $closePicks = $this->getClosestPickMap($leagues, $pickRepo);

        return [
            'leagues' => $leagues,
            'positions' => $positions,
            'leaders' => $leaders,
            'lastPicks' => $lastPicks,
            'closePicks' => $closePicks
        ];
    }

    /**
     * @Route("/join", name="league_join")
     * @Template("@App/league/leagueJoin.html.twig")
     */
    public function joinAction()
    {
        $em = $this->getDoctrine()->getManager();
        $leagues = $em->getRepository('AppBundle:League')->findBy(['isActive' => League::V_ACTIVE]);
        $result = [];

        /** @var League $league */
        foreach ($leagues as $league) {
            if ($league->getIsPrivate()) {
                $result['private'][] = $league;
            } else {
                $result['public'][] = $league;
            }
        }

        return ['leagues' => $result];
    }

    /**
     * @Route("/add", name="league_add")
     * @Template("@App/league/leagueType.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function addAction(Request $request)
    {
        $league = new League();
        return $this->processForm($request, $league);
    }

    /**
     * @Route("/edit/{id}", name="league_edit")
     * @Template("@App/league/leagueType.html.twig")
     *
     * @param Request $request
     * @param League $league
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function editAction(Request $request, League $league)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->createAccessDeniedException();

        }

        return $this->processForm($request, $league);

    }

    /**
     * @param Request $request
     * @param League $league
     *
     * @return array|RedirectResponse
     */
    private function processForm(Request $request, League $league)
    {
        $form = $this->createForm(LeagueType::class, $league);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            /** @var League $league */
            $league = $form->getData();
            $league->setIsActive(League::V_ACTIVE);
            $leagueHasUser = new LeagueHasUser();
            $leagueHasUser->setLeague($league);
            $leagueHasUser->setUser($user);
            $leagueHasUser->setIsLeagueAdmin(true)
                ->setSumOfPoints(0)
                ->setPosition(0);
            $leagueHasUser->setIsActive(LeagueHasUser::V_ACTIVE);

            $options = $league->getOptions();
            $options->setIsActive(true);
            $league->setOptions($options);

            $em = $this->getDoctrine()->getManager();
            $em->persist($league);
            $em->persist($leagueHasUser);
            $em->flush();

            $this->addFlash('success', 'League created! Let\'s play!!!');

            return $this->redirectToRoute('league_list');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}", name="league_show", requirements={"id": "\d+"})
     * @Template("@App/league/leagueShow.html.twig")
     *
     * @param League $league
     *
     * @return array
     */
    public function showAction(League $league)
    {
        $playersData = $league->getLeagueHasUsers();

        return [
            'league' => $league,
            'playersData' => $playersData
        ];
    }

    /**
     * @Route("/join-league-ajax",
     *     name="join_league_ajax",
     *     condition="request.isXmlHttpRequest()",
     *     options={"expose" = true})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxJoinLeagueAction(Request $request)
    {
        $data = $request->request->all();
        $leagueId = isset($data['leagueId']) ? (int)$data['leagueId'] : null;
        $password = isset($data['password']) ? $data['password'] : null;
        try {
            $result = $this->get('app.league.joiner')->validateAndJoinLeague($leagueId, $password);
        } catch (InvalidPasswordException $e) {
            $this->addFlash('error', 'Invalid password! Try again!!!');
            $result = false;
        }
        if ($result) {
            $this->addFlash('success', 'Joined a league! Let\'s play!!!');
        }

        return new JsonResponse($result);
    }

    /**
     * @param League[] $leagues
     * @param LeagueHasUserRepository $leagueHasUserRepo
     *
     * @return array
     */
    private function getPositionsMap(array $leagues, LeagueHasUserRepository $leagueHasUserRepo): array
    {
        $positions = [];
        foreach ($leagues as $league) {
            $positions[$league->getId()] = $leagueHasUserRepo
                ->getPositionForUserInLeague($this->getUser(), $league);
        }
        return $positions;
    }

    /**
     * @param League[] $leagues
     * @param LeagueHasUserRepository $leagueHasUserRepo
     *
     * @return array
     */
    private function getLeadersMap(array $leagues, LeagueHasUserRepository $leagueHasUserRepo): array
    {
        $leaders = [];
        foreach ($leagues as $league) {
            $lhu = $leagueHasUserRepo->findOneBy([
                'league' => $league,
                'position' => LeagueHasUserRepository::LEAGUE_LEADER,
                'isActive' => true
            ]);
            if ($lhu instanceof LeagueHasUser) {
                $username = $lhu->getUser()->getUsername();
                if ($username === $this->getUser()->getUsername()) {
                    $leaders[$league->getId()] = 'YOU (clap)';
                } else {
                    $leaders[$league->getId()] = $username;
                }
            } else {
                $leaders[$league->getId()] = null;
            }
        }
        return $leaders;
    }

    /**
     * @param League[] $leagues
     * @param PickRepository $pickRepository
     *
     * @return array
     */
    private function getLatestPicksMap(array $leagues, PickRepository $pickRepository): array
    {
        $picks = [];
        foreach ($leagues as $league) {
            $pick = $pickRepository->getLatestCountedPick($league, $this->getUser());
            if ($pick instanceof Pick) {
                $picks[$league->getId()]['player'] = $pick->getPlayer()->__toString();
                $picks[$league->getId()]['points'] = $pick->getPoints();
            } else {
                $picks[$league->getId()]['player'] = null;
            }
        }
        return $picks;
    }

    /**
     * @param League[] $leagues
     * @param PickRepository $pickRepository
     *
     * @return array
     */
    private function getClosestPickMap(array $leagues, PickRepository $pickRepository): array
    {
        $picks = [];
        foreach ($leagues as $league) {
            $pick = $pickRepository->getNextPick($league, $this->getUser());
            if ($pick instanceof Pick) {
                $picks[$league->getId()]['player'] = $pick->getPlayer()->__toString();
            } else {
                $picks[$league->getId()]['player'] = null;
            }
        }
        return $picks;
    }
}
