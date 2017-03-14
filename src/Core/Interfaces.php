<?php

namespace Techart\Core;

interface InvokeInterface
{
	public function invoke($args = array());
}

interface PropertyAccessInterface
{
	public function __get($property);
	public function __set($property, $value);
	public function __isset($property);
	public function __unset($property);
}

interface IndexedAccessInterface extends \ArrayAccess
{
}

interface CountInterface extends \Countable
{
}

interface CallInterface
{
	public function __call($method, $args);
}

interface CloneInterface
{
	public function __clone();
}

interface EqualityInterface
{
	public function equals($to);
}

interface StringifyInterface
{
	public function as_string();
	public function __toString();
}

