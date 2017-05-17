<?php

namespace HueBundle\EventListener;

use HueBundle\Services\Integration\UserSyncer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class HueAuthorizeListener
 * @package HueBundle\EventListener
 */
class HueAuthorizeListener
{

    /**
     * @var null|UserSyncer
     */
    private $_userSyncer = null;

    /**
     * HueAuthorizeListener constructor.
     * @param UserSyncer $userSyncer
     */
    public function __construct(UserSyncer $userSyncer)
    {
        $this->_userSyncer = $userSyncer;
    }

    /**
     * @param Event $event
     */
    public function onClientAuthorized(Event $event)
    {
        $this->_userSyncer->sync();
    }
}
