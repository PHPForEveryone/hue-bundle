<?php

namespace HueBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

use \Phue\Command\Ping;
use \Phue\Command\CreateUser;
use \Phue\Transport\Exception\ConnectionException;
use \Phue\Transport\Exception\LinkButtonException;

use HueBundle\Entity\HueUser;
use HueBundle\Factory\HueUserFactory;
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
     * Tries to communicate with hue bridge
     * @var int
     */
    protected $_tries = 30;

    /**
     * @var null|EntityManager
     */
    private $_entityManager = null;

    /**
     * BridgeUser constructor.
     * @param HueSession $session
     * @param HueClient $client
     * @param EntityManager $entityManager
     */
    public function __construct(
        HueSession $session,
        HueClient $client,
        EntityManager $entityManager
    )
    {
        $this->_session = $session;
        $this->_client = $client;
        $this->_entityManager = $entityManager;
    }

    /**
     * Gets the Bridged user
     * @return HueUser|null
     */
    public function getBridgeUser()
    {
        return $this->_session->getUser();
    }

    /**
     * Creates an User
     */
    public function createUser()
    {
        try {
            $client = $this->_client->getClient();
            $client->sendCommand(new Ping);

            for ($i = 1; $i <= $this->_tries; $i++) {
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

    /**
     * Creates the user over ajax
     * @return JsonResponse
     */
    public function createUserAjax()
    {
        // Flush settings workaround to output in realtime..
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();

        try {
            $client = $this->_client->getClient();
            $client->sendCommand(new Ping);

            for ($i = 1; $i <= $this->_tries; $i++) {
                try {
                    $response = $client->sendCommand(new CreateUser);
                    $this->createBridgeUser($response->username);

                    return new JsonResponse([
                        'type'    => 'SUCCESS',
                        'message' => 'User successfully created'
                    ]);
                } catch (LinkButtonException $ex) {
                    $process = $this->calculateProcess($i);
                    echo json_encode([
                        'type'           => 'TRY',
                        'message'        => 'Please press the bridge button within ' . $this->_tries . 'seconds',
                        'process'        => $process,
                        'startSeconds'   => $this->_tries,
                        'currentSeconds' => $i,
                        'remainSeconds'  => $this->getRemainSeconds($i)
                    ]);
                } catch (\Exception $ex) {
                    return new JsonResponse([
                        'type'    => 'ERROR',
                        'message' => $ex->getMessage()
                    ]);
                }

                sleep(1);
            }
        } catch (NoHostException $ex) {
            return new JsonResponse([
                'type'    => 'ERROR',
                'message' => $ex->getMessage()
            ]);
        } catch (ConnectionException $ex) {
            return new JsonResponse([
                'type'    => 'ERROR',
                'message' => 'Unable to connect the bridge'
            ]);
        }

        return new JsonResponse([
            'type'    => 'ERROR',
            'message' => 'User was not created'
        ]);
    }

    /**
     * Calculates the current process
     * @param $counter
     * @return float
     */
    public function calculateProcess($counter)
    {
        return (100 / ($this->_tries / $counter));
    }

    /**
     * Gets the remaining seconds on register process
     * @param $counter
     * @return int
     */
    public function getRemainSeconds($counter)
    {
        return $this->_tries - $counter;
    }

    /**
     * Creates the bridge user
     * @param string $username HueUser username
     * @return HueUser
     */
    public function createBridgeUser($username)
    {
        $user = HueUserFactory::create($username);

        $this->_entityManager->persist($user);
        $this->_entityManager->flush();
    }
}
