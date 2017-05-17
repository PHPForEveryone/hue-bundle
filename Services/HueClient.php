<?php

namespace HueBundle\Services;

use HueBundle\Controller\Exceptions\NoHostException;
use HueBundle\PhueCommands\GetGroups;
use Phue\Command\IsAuthorized;
use Phue\Transport\Exception\ConnectionException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var null|\Phue\Client
     */
    protected $_client = null;

    /**
     * HueClient constructor.
     * @param HueSession $session
     */
    public function __construct(HueSession $session)
    {
        $this->_session = $session;
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

        // Reset connection, may have new user
        if ($this->hasRenewedClient() == false) {
            $this->_client = new \Phue\Client(
                $this->_session->getHost(),
                $this->_session->getUsername()
            );
        }

        return $this->_client;
    }

    /**
     * Gets the groups extension
     * @return mixed
     */
    public function getGroups()
    {
        return $this->getClient()->sendCommand(new GetGroups());
    }

    /**
     * Checks if client is renewed or not
     * @return bool
     */
    public function hasRenewedClient()
    {
        if ($this->_client === null) {
            return false;
        }

        if ($this->_client->getUsername() != $this->_session->getUsername()) {
            return false;
        }

        return true;
    }
}
