<?php

namespace Techart\Core;

class Call implements InvokeInterface
{

	private $call;
	private $args;
	private $cache = array();
	private $enable_cache = false;
	private $autoload;

	/**
	 * @param        $target
	 * @param string $method
	 * @param array  $args
	 */
	public function __construct($target, $method, array $args = array(), $autoload = true)
	{
		if (is_string($target)) {
			$target = Types::real_class_name_for($target);
		}
		$this->autoload = $autoload;
		$this->call = array($target, (string)$method);
		$this->args = $args;
	}

	/**
	 */
	public function update_args($args)
	{
		$this->args = array_merge($this->args, $args);
		return $this;
	}

	public function cache($v = true)
	{
		$this->enable_cache = $v;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function invoke($args = array())
	{
		$args = $this->get_args($args);
		if ($this->enable_cache) {
			$key = serialize($args);
			if (isset($this->cache[$key])) {
				return $this->cache[$key];
			}
			return $this->cache[$key] = call_user_func_array($this->call, $args);
		}
		return call_user_func_array($this->call, $args);
	}

	protected function get_args($values = array())
	{
		return array_merge($this->args, (array)$values);
	}

	public function as_string()
	{
		return 'Core.Call|' . $this->call[0] . '|' . $this->call[1] . '|' . implode('+', $this->args);
	}

	public static function from_string($str)
	{
		if (Strings::starts_with($str, 'Core.Call')) {
			$str = str_replace('Core.Call|', '', $str);
			$parts = explode('|', $str);
			$target = $parts[0];
			$method = $parts[1];
			$args = array();
			if (isset($parts[2])) {
				$args = explode('+', $parts[2]);
			}
			return new static($target, $method, $args);
		}
		return false;
	}

}
