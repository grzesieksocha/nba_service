<?php

namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\LeagueHasUser;
use AppBundle\Form\LeagueType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $em = $this->getDoctrine()->getEntityManager();
        $leagues = $em->getRepository('AppBundle:LeagueHasUser')
            ->getLeaguesForUser($this->getUser());

        return ['leagues' => $leagues];
    }

    /**
     * @Route("/new", name="league_new")
     * @Template("@App/league/leagueAdd.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function newAction(Request $request)
    {
        $league = new League();
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
            $leagueHasUser->setIsLeagueAdmin(true);
            $leagueHasUser->setIsActive(LeagueHasUser::V_ACTIVE);
            $em = $this->getDoctrine()->getManager();
            $em->persist($league);
            $em->persist($leagueHasUser);
            $em->flush();
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}", name="league_show")
     * @Template("@App/league/leagueShow.html.twig")
     *
     * @param League $league
     *
     * @return array
     */
    public function showAction(League $league)
    {
        $players = $league->getUsers();

        return [
            'league' => $league,
            'players' => $players
        ];
    }
}
