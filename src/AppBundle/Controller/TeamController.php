<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Team;
use AppBundle\Form\TeamType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TeamController
 * @package AppBundle\Controller
 * @Route("/team")
 */
class TeamController extends Controller
{
    /**
     * @Route("/list", name="team_list")
     * @Template("@App/team/teamList.html.twig")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('AppBundle:Team')->findAll();

        return ['teams' => $teams];
    }

    /**
     * @Route("/{id}", name="team_show", requirements={"id": "\d+"})
     * @Template("@App/team/teamShow.html.twig")
     *
     * @param Team $team
     *
     * @return array
     */
    public function showAction(Team $team)
    {
        return ['team' => $team];
    }

    /**
     * @Route("/edit/{id}", name="team_edit")
     * @Template("@App/team/teamType.html.twig")
     *
     * @param Request $request
     * @param Team $team
     *
     * @return array
     */
    public function editAction(Request $request, Team $team)
    {
        return $this->processForm($request, $team);
    }

    /**
     * @Route("/add", name="team_add")
     * @Template("@App/team/teamType.html.twig")
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
     * @param Team $team
     *
     * @return array
     */
    private function processForm(Request $request, Team $team = null)
    {
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Team $team */
            $team = $form->getData();
            $team->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            $this->addFlash('success', 'Team created! Let\'s play!!!');

            return $this->redirectToRoute('team_list');
        }

        return ['form' => $form->createView()];
    }
}
