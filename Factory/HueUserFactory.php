<?php

namespace HueBundle\Factory;

use HueBundle\Entity\HueUser;

/**
 * Class HueUserFactory
 * @package HueBundle\Factory
 */
class HueUserFactory
{

    /**
     * Creates an user
     * @param string $username Username
     *
     * @return HueUser
     */
    public static function create($username)
    {
        $user = new HueUser();
        $user->setUsername($username);

        return $user;
    }
}
