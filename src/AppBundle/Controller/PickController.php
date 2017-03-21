<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\Match;
use AppBundle\Entity\Pick;
use AppBundle\Form\PickType;
use AppBundle\Repository\MatchRepository;

use AppBundle\Repository\PickRepository;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use \DateTime;

/**
 * Class PickController
 * @package AppBundle\Controller
 *
 * @Route("/pick")
 */
class PickController extends Controller
{
    /**
     * @Route("/{leagueId}/list", name="pick_list")
     * @Template("AppBundle:pick:pickList.html.twig")
     * @ParamConverter("league", options={"id" = "leagueId"})
     *
     * @param League $league
     *
     * @return array
     */
    public function listAction(League $league)
    {
        $em = $this->getDoctrine()->getManager();
        $picks = $em->getRepository('AppBundle:Pick')->findAllByLeagueAndUser($league, $this->getUser());

        return ['picks' => $picks];
    }

    /**
     * @Route("/new", name="pick_new")
     * @Template("@App/pick/pickType.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function newAction(Request $request)
    {
        return $this->processForm($request);
    }

    /**
     * @param Request $request
     * @param Pick $pick
     *
     * @return array|RedirectResponse
     */
    private function processForm(Request $request, Pick $pick = null)
    {
        $form = $this->createForm(PickType::class, $pick, [
            'match_repository' => $this->getDoctrine()->getRepository('AppBundle:Match'),
            'lhu_repository' => $this->getDoctrine()->getRepository('AppBundle:LeagueHasUser'),
            'user' => $this->getUser()
        ]);

        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Pick $pick */
            $data = $request->request->all();
            $pick = new Pick();
            $pick->setUser($this->getUser())
                ->setLeague($em->getRepository('AppBundle:League')->find($data['pick']['league']))
                ->setMatch($em->getRepository('AppBundle:Match')->find($data['pick']['match']))
                ->setPlayer($em->getRepository('AppBundle:Player')->find($data['pick']['player']))
                ->setPoints(0)
                ->setPointsInLeague(false)
                ->setIsActive(true);

            $em->persist($pick);
            $em->flush();

            $this->addFlash('success', 'Pick saved!');

            return $this->redirectToRoute('pick_new');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/matches",
     *     name="ajax_get_matches",
     *     condition="request.isXmlHttpRequest()",
     *     options={"expose" = true})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetMatchesForDate(Request $request)
    {
        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->get('repository.match');

        $dateTime = $this->getDateToLookForMatches($request->query->keys()[0]);
        $matches = $matchRepo->getAllMatchesForDate($dateTime);

        $result = [];
        $result[0] = 'Now choose teams...';
        foreach ($matches as $match) {
            $result[$match->getId()] =
                $match->getAwayTeam()->getShort() . ' @ ' . $match->getHomeTeam()->getShort();
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/players",
     *     name="ajax_get_players",
     *     condition="request.isXmlHttpRequest()",
     *     options={"expose" = true})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetAvailablePlayersForMatch(Request $request)
    {
        $result = [];
        $ajaxData = $request->query->all();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        /** @var PickRepository $pickRepo */
        $pickRepo = $this->get('repository.pick');
        /** @var Match $match */
        $match = $em->find('AppBundle:Match', $ajaxData['matchId']);
        $league = $em->find('AppBundle:League', $ajaxData['leagueId']);
        $players = $match->getAllPlayers();
        $players = $pickRepo->removeUsedPicks($players, $match, $league, $user);
        foreach ($players as $player) {
            $result[$player->getTeam()->getFullName()][$player->getId()] =
                $player->getFirstName() . ' ' . $player->getLastName();
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/validatePick",
     *     name="ajax_validate_pick",
     *     condition="request.isXmlHttpRequest()",
     *     options={"expose" = true})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxValidatePick(Request $request)
    {
        $data = $request->query->all();
        return new JsonResponse($data);
    }

    /**
     * @param string $date
     *
     * @return DateTime
     */
    private function getDateToLookForMatches(string $date): DateTime
    {
        $date = explode('/', $date);
        $timezone = new DateTimeZone('EST');
        $dateTime = new DateTime('now', $timezone);
        $dateTime->setDate((int)$dateTime->format('Y'), (int)$date[0], (int)$date[1]);
        $dateTime->setTime(0, 0);
        return $dateTime;
    }
}