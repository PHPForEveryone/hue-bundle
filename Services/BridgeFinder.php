<?php

namespace HueBundle\Services;

use GuzzleHttp\Client;
use HueBundle\Controller\Exceptions\BridgeException;

/**
 * Class BridgeFinder
 * @package HueBundle\Services
 */
class BridgeFinder
{

    /**
     * @var Client|null
     */
    private $_client = null;

    /**
     * @var string
     */
    private $_uri = null;

    /**
     * BridgeFinder constructor.
     * @param Client $client
     * @param $uri
     */
    public function __construct(Client $client, $uri)
    {
        $this->_client = $client;
        $this->_uri = $uri;
    }

    /**
     * @return \ArrayIterator
     */
    public function getBridgesList()
    {
        $bridgeList = new \ArrayIterator($this->getBridges());
        return $bridgeList;
    }

    /**
     * Finds all bridges
     * @return \ArrayIterator
     * @throws BridgeException
     */
    public function getBridges()
    {
        $response = $this->_client->get($this->_uri);
        $bridges = $this->toObject(
            $response->getBody()
        );

        if (empty($bridges)) {
            throw new BridgeException('No Bridge found in network');
        }

        return $bridges;
    }

    /**
     * Converts response to object
     * @param $content
     * @return mixed
     */
    public function toObject($content)
    {
        return json_decode($content);
    }
}
