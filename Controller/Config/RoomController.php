<?php

namespace HueBundle\Controller\Config;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class RoomController
 * @package HueBundle\Controller\Config
 */
class RoomController extends Controller
{

    /**
     * Shows the bridge page (/hue/config/room)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@Hue/Config/Room/index.html.twig');
    }
}
