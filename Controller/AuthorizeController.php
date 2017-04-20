<?php

namespace HueBundle\Controller;

use GuzzleHttp\Exception\ConnectException;
use HueBundle\Controller\Exceptions\BridgeException;
use HueBundle\Services\BridgeFinder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AuthorizeController
 * @package HueBundle\Controller
 */
class AuthorizeController extends Controller
{

    public function indexAction()
    {
        return $this->render('HueBundle:Authorize:index.html.twig', [
            'bridges' => $this->getBridgesList()
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
