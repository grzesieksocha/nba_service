<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\Pick;
use AppBundle\Form\PickType;
use AppBundle\Repository\MatchRepository;

use AppBundle\Repository\PickRepository;
use AppBundle\Repository\PlayerRepository;
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
    * @Route("{leagueId}/list", name="pick_list")
    * @Template("AppBundle:pick:pickList.html.twig")
    */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $matches = $em->getRepository('AppBundle:Match')->findAll();

        return ['matches' => $matches];
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
                ->setIsActive(true);

            $em->persist($pick);
            $em->flush();

            $this->addFlash('success', 'Match added!');

            return $this->redirectToRoute('match_list');
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
        $matches = $matchRepo->getAllMatchesForDate(
            $this->getDateToCheck($request->query->keys())
        );
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
        /** @var PlayerRepository $matchRepo */
        $matchRepo = $this->get('repository.match');
        /** @var PickRepository $pickRepo */
        $pickRepo = $this->get('repository.pick');
        $match = $em->find('AppBundle:Match', $ajaxData['matchId']);
        $league = $em->find('AppBundle:League', $ajaxData['leagueId']);
        $players = $match->getAllPlayers();
        $players = $pickRepo->removeUsedPicks($players, $match, $league, $user);
        foreach ($players as $player) {
            $result[$player->getId()] =
                $player->getFirstName() . ' ' . $player->getLastName();
        }
        return new JsonResponse($result);
    }

    /**
     * @param array $dateFromRequest
     *
     * @return DateTime
     */
    private function getDateToCheck(array $dateFromRequest) {
        $dateFromRequest = explode('/', $dateFromRequest[0]);
        $dateToCheck = new DateTime();
        $dateToCheck->setDate(2017, (int)$dateFromRequest[0], (int)$dateFromRequest[1]);
        $dateToCheck->setTime(0, 0);
        return $dateToCheck;
    }
}