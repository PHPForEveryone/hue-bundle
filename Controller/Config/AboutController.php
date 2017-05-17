<?php

namespace HueBundle\Controller\Config;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AboutController
 * @package HueBundle\Controller\Config
 */
class AboutController extends Controller
{

    /**
     * Shows the about page (/hue/config/about)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@Hue/Config/about.html.twig');
    }
}
