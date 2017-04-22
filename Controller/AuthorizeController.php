<?php

namespace HueBundle\Controller;

use HueBundle\Controller\Exceptions\BridgeException;
use HueBundle\Services\BridgeFinder;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use GuzzleHttp\Exception\ConnectException;

/**
 * Class AuthorizeController
 * @package HueBundle\Controller
 */
class AuthorizeController extends Controller
{

    public function indexAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('HueBundle:Authorize:index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'bridges'       => $this->getBridgesList(),
        ]);
    }

    /**
     * Gets the bridges list if available
     * @return \ArrayIterator|array
     */
    public function getBridgesList()
    {
        /** @var BridgeFinder $finder */
        $finder = $this->get('hue.bridge.finder');
        $bridges = [];

        try {
            $bridges = $finder->getBridgesList();
        } catch (ConnectException $ex) {
            $this->addFlash('error', 'No internet connection');
        } catch (BridgeException $ex) {
            $this->addFlash('error', 'No bridge found');
        } finally {
            return $bridges;
        }
    }
}
