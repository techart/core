<?php

namespace Techart\Core;

/**
 * Interface InvokeInterface
 * @package Techart\Core
 */
interface InvokeInterface
{
    /**
     * @param array $args
     * @return mixed
     */
    public function invoke($args = array());
}

/**
 * Interface PropertyAccessInterface
 * @package Techart\Core
 */
interface PropertyAccessInterface
{
    /**
     * @param $property
     * @return mixed
     */
    public function __get($property);

    /**
     * @param $property
     * @param $value
     * @return mixed
     */
    public function __set($property, $value);

    /**
     * @param $property
     * @return mixed
     */
    public function __isset($property);

    /**
     * @param $property
     * @return mixed
     */
    public function __unset($property);
}

/**
 * Interface IndexedAccessInterface
 * @package Techart\Core
 */
interface IndexedAccessInterface extends \ArrayAccess
{
}

/**
 * Interface CountInterface
 * @package Techart\Core
 */
interface CountInterface extends \Countable
{
}

/**
 * Interface CallInterface
 * @package Techart\Core
 */
interface CallInterface
{
    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args);
}

/**
 * Interface CloneInterface
 * @package Techart\Core
 */
interface CloneInterface
{
    /**
     * @return mixed
     */
    public function __clone();
}

/**
 * Interface EqualityInterface
 * @package Techart\Core
 */
interface EqualityInterface
{
    /**
     * @param $to
     * @return mixed
     */
    public function equals($to);
}

/**
 * Interface StringifyInterface
 * @package Techart\Core
 */
interface StringifyInterface
{
    /**
     * @return mixed
     */
    public function as_string();

    /**
     * @return mixed
     */
    public function __toString();
}

