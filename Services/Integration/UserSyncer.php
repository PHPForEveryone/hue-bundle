<?php

namespace HueBundle\Services\Integration;

use HueBundle\Entity\HueUser;
use HueBundle\Factory\HueUserFactory;
use HueBundle\Services\HueClient;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;

/**
 * Class UserSyncer
 * @package HueBundle\Services\Integration
 */
class UserSyncer
{

    /**
     * @var null|EntityManager
     */
    private $_entityManager = null;

    /**
     * @var HueClient|null
     */
    private $_client = null;

    /**
     * @var null|LoggerInterface
     */
    private $_logger = null;

    /**
     * Users which will be deleted
     * @var array
     */
    protected $_outdated = [];

    /**
     * UserSyncer constructor.
     * @param EntityManager $entityManager
     * @param HueClient $client
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $entityManager, HueClient $client, LoggerInterface $logger)
    {
        $this->_entityManager = $entityManager;
        $this->_client = $client;
        $this->_logger = $logger;
    }

    /**
     * Syncs the user from bridge and the user from app if any differents appear, this function
     * will filter that an sync both apps
     */
    public function sync()
    {
        $this->_compareUsers();
        $this->_deleteOutdatedUsers();
        $this->_createMissingUsers();
    }

    /**
     * Compares the users between bridge and app
     */
    protected function _compareUsers()
    {
        $repoUsers = $this->getRepoUsers();
        foreach ($repoUsers as $repoUser) {
            $this->_findOutdatedByUser($repoUser);
        }
    }

    /**
     * Finds the outdated users
     * @param HueUser $user
     */
    protected function _findOutdatedByUser(HueUser $user)
    {
        $username = $user->getUsername();
        if (in_array($username, $this->getCurrentBridgeUsernames()) === false) {
            $this->_outdated[] = $user;
        }
    }

    /**
     * Gets the repo users (apps user)
     *
     * We do not cache repo users to have instant up-to-date data
     *
     * @return array|HueUser[]
     */
    public function getRepoUsers()
    {
        $repo = $this->_entityManager->getRepository('HueBundle:HueUser');
        return $repo->findAll();
    }

    /**
     * Gets the usernames from users in repo
     * @return array
     */
    public function getRepoUsernames()
    {
        return array_map(function ($user) {
            /** @var HueUser $user */
            return $user->getUsername();
        }, $this->getRepoUsers());
    }

    /**
     * Gets the bridge user list
     * @return \Phue\User[]
     */
    public function getBridgeUsers()
    {
        return $this->_client->getClient()->getUsers();
    }

    /**
     * Gets the current bridge usernames
     * @return array
     */
    public function getCurrentBridgeUsernames()
    {
        return array_keys($this->getBridgeUsers());
    }

    /**
     * Deletes outdated users instantly
     */
    protected function _deleteOutdatedUsers()
    {
        if (!$this->_outdated) {
            return;
        }

        foreach ($this->_outdated as $deleteable) {
            try {
                $this->_entityManager->remove($deleteable);
                $this->_entityManager->flush();
                $this->_logger->info('UserSyncer - Outdated user was successfully deleted!');
            } catch (\Exception $ex) {
                $this->_logger->warning("UserSyncer - Outdated user could not be deleted! ({$ex->getMessage()})");
            }
        }
    }

    /**
     * Create the users which in bridge registered but not in our app
     */
    protected function _createMissingUsers()
    {
        $available = $this->getRepoUsernames();
        $current = $this->getCurrentBridgeUsernames();

        $includeables = array_diff($current, $available);

        foreach ($includeables as $includeable) {
            $user = HueUserFactory::create($includeable);

            try {
                $this->getRefreshedManager()->persist($user);
                $this->getRefreshedManager()->flush();
                $this->_logger->info('UserSyncer - Missing user was successfully created!');
            } catch (UniqueConstraintViolationException $ex) {
                $this->_logger->info("UserSyncer - Missing user was already in Database! (User: {$user->getUsername()})");
                continue;
            }
        }
    }

    /**
     * @return EntityManager|null
     */
    private function getRefreshedManager()
    {
        if (!$this->_entityManager->isOpen()) {
            return $this->_entityManager->create(
                $this->_entityManager->getConnection(),
                $this->_entityManager->getConfiguration()
            );
        }

        return $this->_entityManager;
    }
}
