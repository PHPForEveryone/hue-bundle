<?php

namespace HueBundle\Controller\Config;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BridgeController
 * @package HueBundle\Controller\Config
 */
class BridgeController extends Controller
{

    /**
     * Shows the bridge page (/hue/config/bridge)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@Hue/Config/Bridge/index.html.twig');
    }
}
