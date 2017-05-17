<?php

namespace HueBundle\Controller;

use HueBundle\Security\User\HueUserProvider;

use HueBundle\Services\BridgeUser;
use HueBundle\Services\HueSession;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AuthorizeController
 * @package HueBundle\Controller
 */
class AuthorizeController extends Controller
{

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        // Check host
        if ($this->hasHostToAuthorize() == false) {
            return new RedirectResponse(
                $this->generateUrl('hue_choose_bridge')
            );
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        /** @var HueUserProvider $userProvider */
        $userProvider = $this->get('hue.user.provider');
        $users = $userProvider->getAvailableUsers();

        return $this->render('HueBundle:Authorize:index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'users'         => $users
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxAction(Request $request)
    {
        /** @var BridgeUser $user */
        $user = $this->get('hue.bridge.user');
        $response = $user->createUserAjax();

        return $response;
    }

    /**
     * Checks if a host was setted
     * @return bool
     */
    public function hasHostToAuthorize()
    {
        /** @var HueSession $session */
        $session = $this->get('hue.session');
        return $session->hasHost();
    }
}
