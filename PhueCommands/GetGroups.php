<?php

namespace HueBundle\PhueCommands;

use Phue\Client;
use Phue\Command\GetGroups as PhueGetGroups;
use Phue\Command\CommandInterface as PhueCommandInterface;

/**
 * Get groups command extension
 */
class GetGroups extends PhueGetGroups implements PhueCommandInterface
{

    /**
     * Sends a command to Phue client
     *
     * @param Client $client Phue Client
     * @return Group[] List of Group objects
     */
    public function send(Client $client)
    {
        // Get response
        $results = $client->getTransport()->sendRequest(
            "/api/{$client->getUsername()}/groups"
        );

        $groups = array();

        foreach ($results as $groupId => $attributes) {
            $groups[$groupId] = new Group($groupId, $attributes, $client);
        }

        return $groups;
    }
}
