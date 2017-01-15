<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("@App/Main/main.html.twig")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        $lastUsernameKey = Security::LAST_USERNAME;

        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return [
            'last_username' => $lastUsername,
            'csrf_token' => $csrfToken
        ];
    }
}
