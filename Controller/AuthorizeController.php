<?php

namespace HueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthorizeController extends Controller
{

    public function indexAction()
    {
        $client = $this->get('hue.client');

        return $this->render('HueBundle:Authorize:index.html.twig');
    }

    protected function test()
    {


        $response = @file_get_contents('http://www.meethue.com/api/nupnp');

        // Don't continue if bad response
        if ($response === false) {
            echo "\tRequest failed. Ensure that you have internet connection.";
            exit(1);
        }

        echo "\tRequest succeeded", "\n\n";

        // Parse the JSON response
        $bridges = json_decode($response);

        echo "Number of bridges found: ", count($bridges), "\n";

        // Iterate through each bridge
        foreach ($bridges as $key => $bridge) {
            echo "\tBridge #", ++$key, "\n";
            echo "\t\tID: ", $bridge->id, "\n";
            echo "\t\tInternal IP Address: ", $bridge->internalipaddress, "\n";
            echo "\t\tMAC Address: ", $bridge->macaddress, "\n";
            echo "\n";
        }

    }
}
