<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\Pick;
use AppBundle\Form\PickType;
use AppBundle\Repository\MatchRepository;

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
            'match_repository' => $this->getDoctrine()->getRepository('AppBundle:Match')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Pick $pick */
            $pick = $form->getData();
            $pick->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
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
        foreach ($matches as $match) {
            $result[$match->getId()] =
                $match->getAwayTeam()->getShort() . ' @ ' . $match->getHomeTeam()->getShort();
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