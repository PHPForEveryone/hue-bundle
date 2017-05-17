<?php

namespace HueBundle\Security;

use HueBundle\Services\HueSession;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class HueUserAuthenticator
 * @package HueBundle\Security
 */
class HueUserAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var RouterInterface|null
     */
    private $_router = null;

    /**
     * @var HueSession|null
     */
    private $_session = null;

    /**
     * @var null|EventDispatcherInterface
     */
    private $_dispatcher = null;

    /**
     * HueUserAuthenticator constructor.
     * @param RouterInterface $router
     * @param HueSession $session
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(RouterInterface $router, HueSession $session, EventDispatcherInterface $dispatcher)
    {
        $this->_router = $router;
        $this->_session = $session;
        $this->_dispatcher = $dispatcher;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {

        if (!$username = $request->get('_username')) {
            // no token? Return null and no other methods will be called
            return;
        }

        // What you return here will be passed to getUser() as $credentials
        return array(
            'token' => $username,
        );
    }

    /**
     * Gets the user
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['token'];

        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $userProvider->loadUserByUsername($username);
    }

    /**
     * Checks the credentials
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    /**
     * Calls on authentication success
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->_session->setUser($token);
        $this->_dispatcher->dispatch('client.authorized');

        $targetPath = $request->get('_target_path');
        return new RedirectResponse($targetPath);
    }

    /**
     * Calls on authentication failure
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->getSession() instanceof SessionInterface) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $url = $request->get('_redirect_path');
        return new RedirectResponse($url);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($request->getSession() instanceof SessionInterface) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $authException);
        }

        $url = $this->_router->generate('hue_homepage');
        return new RedirectResponse($url, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
