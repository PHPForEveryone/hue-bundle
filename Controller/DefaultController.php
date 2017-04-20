<?php

namespace HueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('HueBundle:Default:index.html.twig');
    }
}
