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
     * @Route("/{id}", name="team_show")
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
     * @Template("@App/team/teamForm.html.twig")
     */
    public function editAction(Team $team)
    {
    }

    /**
     * @Route("/add", name="team_add")
     * @Template("@App/team/teamShow.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(TeamType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }


        return ['form' => $form->createView()];
    }
}
