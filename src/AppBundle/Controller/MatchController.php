<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Match;
use AppBundle\Form\MatchType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        #TODO players statistics in a beautiful table!
        return ['match' => $match];
    }

    /**
     * @Route("/edit/{id}", name="match_edit")
     * @Template("@App/match/matchType.html.twig")
     *
     * @param Request $request
     * @param Match $match
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
     * @param Match $match
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
}
