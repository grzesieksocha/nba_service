<?php declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PlayerController
 * @package AppBundle\Controller
 * @Route("/player")
 */
class PlayerController extends Controller
{
    /**
     * @Route("/list", name="player_list")
     * @Template("@App/player/playerList.html.twig")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();

        return ['players' => $players];
    }

    /**
     * @Route("/{id}", name="player_show", requirements={"id": "\d+"})
     * @Template("@App/player/playerShow.html.twig")
     *
     * @param Player $player
     *
     * @return array
     */
    public function showAction(Player $player)
    {
        return ['player' => $player];
    }

    /**
     * @Route("/edit/{id}", name="player_edit")
     * @Template("@App/player/playerType.html.twig")
     *
     * @param Request $request
     * @param Player $player
     *
     * @return array
     */
    public function editAction(Request $request, Player $player)
    {
        return $this->processForm($request, $player);
    }

    /**
     * @Route("/add", name="player_add")
     * @Template("@App/player/playerType.html.twig")
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
     * @param Player $player
     *
     * @return array|RedirectResponse
     */
    private function processForm(Request $request, Player $player = null)
    {
        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Player $player */
            $player = $form->getData();
            $player->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            $this->addFlash('success', 'Player added!');

            return $this->redirectToRoute('player_list');
        }

        return ['form' => $form->createView()];
    }
}
