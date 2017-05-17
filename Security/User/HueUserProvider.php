<?php

namespace HueBundle\Security\User;

use Doctrine\ORM\EntityManager;
use HueBundle\Entity\HueUser;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class HueUserProvider
 * @package HueBundle\Security\User
 */
class HueUserProvider implements UserProviderInterface
{

    /**
     * @var null|EntityManager
     */
    private $_entityManager = null;

    /**
     * HueUserProvider constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * @param string $username
     * @return HueUser
     */
    public function loadUserByUsername($username)
    {
        $repository = $this->_entityManager->getRepository('HueBundle:HueUser');
        /** @var HueUser $user */
        $user = $repository->findOneBy([
            'username' => $username
        ]);

        if ($user === null) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }

        return $user;
    }

    /**
     * Gets the available users
     * @return array|HueUser[]
     */
    public function getAvailableUsers()
    {
        $repository = $this->_entityManager->getRepository('HueBundle:HueUser');
        return $repository->findAll();
    }

    /**
     * Refreshes the hue user
     * @param UserInterface $user
     * @return HueUser
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof HueUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return HueUser::class === $class;
    }
}
