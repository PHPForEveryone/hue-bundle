<?php

namespace HueBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class HueSession
 * @package HueBundle\Services
 */
class HueSession
{

    /**
     * @var null|Session
     */
    private $_session = null;

    /**
     * @var null
     */
    protected $_user = null;

    /**
     * HueSession constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->_session = $session;
    }

    /**
     * Gets the session
     * @return null|Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Sets the host
     * @param string $host
     */
    public function setHost($host)
    {
        $this->_session->set('hue_host', $host);
    }

    /**
     * Gets the host
     * @return string
     */
    public function getHost()
    {
        return $this->_session->get('hue_host');
    }

    /**
     * Checks if host is set
     * @return bool
     */
    public function hasHost()
    {
        return $this->_session->has('hue_host');
    }

    /**
     * Gets an user
     * @return mixed
     */
    public function getUser()
    {
        if ($this->hasHost() == false) {
            return null;
        }

        return $this->_session->get('user');
    }

    /**
     * Gets the users name
     * @return string
     */
    public function getUsername()
    {
        /** @var TokenInterface $user */
        if ($user = $this->getUser()) {
            return $user->getUsername();
        }

        return '';
    }

    /**
     * Checks if user is available
     * @return bool
     */
    public function hasUser()
    {
        return $this->_session->has('user');
    }

    /**
     * Sets an user
     * @param mixed $user
     */
    public function setUser($user)
    {
        return $this->_session->set('user', $user);
    }

    /**
     * Flashes an error
     * @param string $message
     */
    public function flashError($message)
    {
        $this->_session->getFlashBag()->add('error', $message);
    }

    /**
     * Flashes a success
     * @param string $message
     */
    public function flashSuccess($message)
    {
        $this->_session->getFlashBag()->add('success', $message);
    }
}
