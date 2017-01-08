<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("@App/Main/main.html.twig")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('league_list');
    }
}
