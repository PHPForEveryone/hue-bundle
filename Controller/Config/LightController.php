<?php

namespace HueBundle\Controller\Config;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class LightController
 * @package HueBundle\Controller\Config
 */
class LightController extends Controller
{

    /**
     * Shows the bridge page (/hue/config/light)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@Hue/Config/Light/index.html.twig');
    }
}
