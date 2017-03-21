<?php

namespace Techart\Core;

/**
 * Class Service
 * @package Techart\Core
 */
class Service
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param $name
     * @param bool|true $value
     */
    public function setOption($name, $value = true)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param $name
     * @return null
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     *
     */
    public function init()
    {

    }

    /**
     * @return $this
     */
    public function service()
    {
        return $this;
    }
}