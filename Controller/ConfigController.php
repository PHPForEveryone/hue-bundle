<?php

namespace HueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ConfigController
 * @package HueBundle\Controller
 */
class ConfigController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('HueBundle:Config:index.html.twig');
    }
}
