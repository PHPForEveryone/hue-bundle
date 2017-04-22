<?php

namespace HueBundle\Services;

use \Phue\Command\Ping;
use \Phue\Command\CreateUser;
use \Phue\Transport\Exception\ConnectionException;
use \Phue\Transport\Exception\LinkButtonException;

use HueBundle\Controller\Exceptions\NoHostException;

/**
 * Class BridgeUser
 * @package HueBundle\Services
 */
class BridgeUser
{

    /**
     * @var null|HueSession
     */
    private $_session = null;

    /**
     * @var null|HueClient
     */
    private $_client = null;

    /**
     * BridgeUser constructor.
     * @param HueSession $session
     * @param HueClient $client
     */
    public function __construct(HueSession $session, HueClient $client)
    {
        $this->_session = $session;
        $this->_client = $client;
    }

    public function getUsername()
    {

    }

    public function createUser()
    {
        try {
            $client = $this->_client->getClient();
            $client->sendCommand(new Ping);

            for ($i = 1; $i <= 30; ++$i) {
                try {
                    $response = $client->sendCommand(new CreateUser);

                    var_dump($response);

                    $this->_session->setUser($response->username);
                    $this->_session->flashSuccess('Successfully created an user');
                    // @todo: create user model
                    return;
                } catch (LinkButtonException $ex) {
                    echo ".";
                } catch (\Exception $ex) {
                    $this->_session->flashError($ex->getMessage());
                    break;
                }

                sleep(1);
            }
        } catch (NoHostException $ex) {
            $this->_session->flashError($ex->getMessage());
        } catch (ConnectionException $ex) {
            $this->_session->flashError('Unable to connect the bridge');
        }
    }
}
