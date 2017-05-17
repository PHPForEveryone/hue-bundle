<?php

namespace HueBundle\Controller;

use HueBundle\Services\HueSession;
use HueBundle\Services\BridgeFinder;
use HueBundle\Controller\Exceptions\BridgeException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use GuzzleHttp\Exception\ConnectException;

/**
 * Class AuthorizeController
 * @package HueBundle\Controller
 */
class BridgeController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('HueBundle:Bridge:index.html.twig', [
            'bridges' => $this->getBridgesList(),
        ]);
    }

    /**
     * Chooses the host-bridge
     * @param Request $request
     * @return RedirectResponse
     */
    public function chooseAction(Request $request)
    {
        $host = $request->get('_host');

        /** @var HueSession $session */
        $session = $this->get('hue.session');
        $session->setHost($host);

        return new RedirectResponse($this->generateUrl('hue_authorize'));
    }

    /**
     * Gets the bridges list if available
     * @return \ArrayIterator|array
     */
    public function getBridgesList()
    {
        /** @var BridgeFinder $finder */
        $finder = $this->get('hue.bridge.finder');
        $translator = $this->get('translator');
        $bridges = [];

        try {
            $bridges = $finder->getBridgesList();
        } catch (ConnectException $ex) {
            $this->addFlash('error', $translator->trans('BridgeErrorNoConnection'));
        } catch (BridgeException $ex) {
            $this->addFlash('error', $translator->trans('BridgeErrorNotFound'));
        } finally {
            return $bridges;
        }
    }
}
