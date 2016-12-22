<?php

namespace AppBundle\Controller;

use AppBundle\DataImporter\TeamsDataImporter;
use AppBundle\DataImporter\TeamsSaver;
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
    }
}
