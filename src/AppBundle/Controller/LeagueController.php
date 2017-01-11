<?php

namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\LeagueHasUser;
use AppBundle\Form\LeagueType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $leagues = $em->getRepository('AppBundle:LeagueHasUser')
            ->getLeaguesForUser($this->getUser());

        return ['leagues' => $leagues];
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
     * @return array
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
