<?php

namespace HueBundle\Services;

use HueBundle\Controller\Exceptions\NoHostException;

/**
 * Class HueClient
 * @package HueBundle\Services
 */
class HueClient
{

    /**
     * @var null|HueSession
     */
    private $_session = null;

    /**
     * @var string
     */
    private $_username = '';

    /**
     * @var null|\Phue\Client
     */
    protected $_client = null;

    /**
     * HueClient constructor.
     * @param HueSession $session
     * @param string $username
     */
    public function __construct(HueSession $session, $username)
    {
        $this->_session = $session;
        $this->_username = $username;
    }

    /**
     * Gets the Phue client
     * @return null|\Phue\Client
     * @throws NoHostException
     */
    public function getClient()
    {
        if ($this->_session->hasHost() === false) {
            throw new NoHostException('You have to set a host');
        }

        if ($this->_client === null) {
            $this->_client = new \Phue\Client(
                $this->_session->getHost()
            );
        }

        return $this->_client;
    }
}
