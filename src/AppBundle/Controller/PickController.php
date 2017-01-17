<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Match;
use AppBundle\Entity\Pick;
use AppBundle\Form\PickType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
}