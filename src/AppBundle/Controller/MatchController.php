<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Match;
use AppBundle\Entity\Statistics;
use AppBundle\Form\MatchType;

use AppBundle\Repository\MatchRepository;
use DateTime;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MatchController
 *
 * @package AppBundle\Controller
 * @Route("/match")
 */
class MatchController extends Controller
{
    /**
     * @Route("/list", name="match_list")
     * @Template("AppBundle:match:matchList.html.twig")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $matches = $em->getRepository('AppBundle:Match')->findAll();

        return ['matches' => $matches];
    }

    /**
     * @Route("/{id}", name="match_show", requirements={"id": "\d+"})
     * @Template("@App/match/matchShow.html.twig")
     *
     * @param Match $match
     *
     * @return array
     */
    public function showAction(Match $match)
    {
        $em = $this->getDoctrine()->getManager();
        $allStats = $em->getRepository('AppBundle:Statistics')->getStatsForMatch($match);
        $stats = [];
        /** @var Statistics $statistic */
        foreach ($allStats as $statistic) {
            $stats[$statistic->getPlayer()->getId()] = $statistic;
        }

        return ['match' => $match, 'stats' => $stats];
    }

    /**
     * @Route("/edit/{id}", name="match_edit")
     * @Template("@App/match/matchType.html.twig")
     *
     * @param Request $request
     * @param Match   $match
     *
     * @return array
     */
    public function editAction(Request $request, Match $match)
    {


        return $this->processForm($request, $match);
    }

    /**
     * @Route("/add", name="match_add")
     * @Template("@App/match/matchType.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        return $this->processForm($request);
    }

    /**
     * @param Request $request
     * @param Match   $match
     *
     * @return array|RedirectResponse
     */
    private function processForm(Request $request, Match $match = null)
    {
        $form = $this->createForm(MatchType::class, $match);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Match $match */
            $match = $form->getData();
            $match->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($match);
            $em->flush();

            $this->addFlash('success', 'Match added!');

            return $this->redirectToRoute('match_list');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/data",
     *     name="ajax_get_matches_for_list",
     *     condition="request.isXmlHttpRequest()",
     *     options={"expose" = true})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetMatchesForList(Request $request)
    {
        $result = [];
        $data = $request->query->all();
        $timezone = new DateTimeZone('EST');
        $dateFrom = new DateTime($data['dateFrom'], $timezone);
        $dateTo = new DateTime($data['dateTo'], $timezone);

        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->get('repository.match');
        $matches = $matchRepo->getAllMatchesForDateRange($dateFrom, $dateTo);
        $index = 1;
        foreach ($matches as $match) {
            $result[$index] = $this->getMatchProperties($match);
            $index++;
        }
        return new JsonResponse($result);
    }

    /**
     * @param Match $match
     *
     * @return string[]
     */
    private function getMatchProperties(Match $match)
    {
        $result['id'] = $match->getId();
        $result['homeTeam'] = $match->getHomeTeam()->getFullName();
        $result['awayTeam'] = $match->getAwayTeam()->getFullName();
        $result['homeTeamPoints'] = $match->getHomeTeamPoints();
        $result['awayTeamPoints'] = $match->getAwayTeamPoints();
        $result['date'] = $match->getDate()->format('d/m H:i');
        return $result;
    }
}
