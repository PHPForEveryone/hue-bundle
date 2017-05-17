<?php

namespace HueBundle\Controller;

use HueBundle\Services\HueClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package HueBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * @return Response|RedirectResponse
     */
    public function indexAction()
    {
        $session = $this->get('hue.session');

        /** @var HueClient $client */
        $client = $this->get('hue.client');
        $lights = $client->getClient()->getLights();
        $groups = $client->getGroups();

        return $this->render('HueBundle:Default:index.html.twig', [
            'token'     => $session->getUser(),
            'lights'    => $lights,
            'groups'    => $groups,
        ]);
    }

    /**
     * Logs the user out
     */
    public function logoutAction()
    {
        $this->get('hue.session')->getSession()->invalidate();
        $this->get('security.token_storage')->setToken(null);

        return $this->redirect(
            $this->generateUrl('hue_homepage')
        );
    }
}
