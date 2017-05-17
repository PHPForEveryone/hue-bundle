<?php

namespace HueBundle\Controller\Config;

use HueBundle\Services\HueClient;

use Phue\Command\DeleteUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserController
 * @package HueBundle\Controller\Config
 */
class UserController extends Controller
{

    /**
     * Shows the users page (/hue/config/user)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var HueClient $client */
        $client = $this->get('hue.client');
        $users = $client->getClient()->getUsers();

        return $this->render('@Hue/Config/User/index.html.twig', [
            'users' => $users
        ]);
    }

    public function updateAction()
    {

    }

    public function deleteAction($username)
    {
        $client = $this->get('hue.client');
        $client->getClient()->sendCommand(new DeleteUser($username));
    }
}
