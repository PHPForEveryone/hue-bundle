<?php

namespace HueBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hue_user")
 */
class HueUser implements UserInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $apiKey;

    /**
     * Gets the id
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the username
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gets the user role
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * Gets the api key
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}
