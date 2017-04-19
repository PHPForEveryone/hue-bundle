<?php

namespace HueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthorizeController extends Controller
{

    public function indexAction()
    {
        return $this->render('HueBundle:Authorize:index.html.twig');
    }
}
