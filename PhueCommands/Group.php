<?php

namespace HueBundle\PhueCommands;

use \Phue\Group as PhueGroup;

/**
 * Class Group
 * @package HueBundle\PhueCommands
 */
class Group extends PhueGroup
{

    /**
     * Gets the attributes
     * @return \stdClass
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Gets the class from room type
     * @return mixed
     */
    public function getClass()
    {
        return preg_replace('/\W+/', '', strtolower($this->attributes->class));
    }
}
