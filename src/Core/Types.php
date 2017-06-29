<?php

namespace Techart\Core;

/**
 * Набор методов для работы с информацией о типах
 *
 */
class Types
{

	/**
	 * Проверяет, является ли переданное значение массивом
	 *
	 * @param  $object
	 *
	 * @return boolean
	 */
	public static function is_array(&$object)
	{
		return is_array($object);
	}

	/**
	 * Проверяет, является ли переданное значение строкой
	 *
	 * @param  $object
	 *
	 * @return boolean
	 */
	public static function is_string(&$object)
	{
		return is_string($object);
	}

	/**
	 * Проверяет, является ли переданное значение числом
	 *
	 * @param  $object
	 *
	 * @return boolean
	 */
	public static function is_number(&$object)
	{
		return is_numeric($object);
	}

	/**
	 * Проверяет является ли переданное значение объектом
	 *
	 * @param  $object
	 *
	 * @return boolean
	 */
	public static function is_object(&$object)
	{
		return is_object($object);
	}

	/**
	 * Проверяет является ли переданное значение ресурсом
	 *
	 * @param  $object
	 *
	 * @return boolean
	 */
	public static function is_resource(&$object)
	{
		return is_resource($object);
	}

	/**
	 * Проверяет является ли переданное значение итерируемым объектом.
	 *
	 * @param  $object
	 *
	 * @return boolean
	 */
	public static function is_iterable(&$object)
	{
		return is_array($object) || $object instanceof \Traversable;
	}

	/**
	 * Проверяет является ли данный класс данного объект наследником заданного класса
	 *
	 * @param  $ancestor
	 * @param  $object
	 *
	 * @return boolean
	 */
	public static function is_subclass_of($ancestor, $object)
	{
		$ancestor_class = self::real_class_name_for($ancestor);
		if (is_object($object)) {
			return ($object instanceof $ancestor_class);
		}

		$object_class = self::real_class_name_for($object);
		if (!class_exists($object_class, false)) {
			return false;
		}

		//return $object_class instanceof $ancestor_class;

		//TODO: remove:
		$object_reflection = new \ReflectionClass($object_class);

		return \Techart\Core::with($ancestor_reflection = new \ReflectionClass($ancestor_class))->isInterface() ?
			$object_reflection->implementsInterface($ancestor_class) :
			$object_reflection->isSubclassOf($ancestor_reflection);
	}

	/**
	 * Возвращает имя класса для объекта
	 *
	 * @param         $object
	 *
	 * @return string
	 */
	static function class_name_for($object)
	{
		$className = is_object($object) ?
			get_class($object) : (
			is_string($object) ? $object : null);

		if (!$className) {
			return null;
		}

		$className = str_replace('.', '\\', trim($className, '.'));
		return '\\'. ltrim($className, '\\');
	}

	/**
	 * Вовзращает действительное имя класса для заданного объекта
	 *
	 * @param  $object
	 *
	 * @return string
	 */
	public static function real_class_name_for($object)
	{
		return self::class_name_for($object, false);
	}

	/**
	 * Возвращает имя модуля для заданного объекта
	 *
	 * @param  $object
	 *
	 * @return string
	 */
	public static function module_name_for($object)
	{
		return preg_replace('{\.[^.]+$}', '', self::class_name_for($object, true));
	}

	/**
	 * Возвращает reflection для заданного объекта или класса
	 *
	 * @param  $object
	 *
	 * @return mixed
	 */
	public static function reflection_for($object)
	{
		if (Types::is_string($object)) {
			return new \ReflectionClass(self::real_class_name_for($object));
		}

		if (Types::is_object($object)) {
			return new \ReflectionObject($object);
		}

		throw new \Techart\Core\InvalidArgumentTypeException('object', $object);
	}

	/**
	 * Возвращает список классов, составляющих иерархию наследования для данного объекта.
	 *
	 * @param object|string $object
	 * @param boolean       $use_virtual_names
	 *
	 * @return array
	 */
	public static function class_hierarchy_for($object, $use_virtual_names = false)
	{
		$class = is_string($object) ? str_replace('.', '_', $object) : get_class($object);

		if ($use_virtual_names) {
			$r = array(str_replace('_', '.', $class));
			foreach (class_parents($class) as $c)
				$r[] = str_replace('_', '.', $c);
			return $r;
		} else {
			return array_merge(array($class), array_keys(class_parents($class)));
		}
	}

	/**
	 * Проверяет существует ли класс с заданным именем
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public static function class_exists($name)
	{
		return class_exists(self::class_name_for((string)$name, false));
	}

	public static function is_callable($value)
	{
		return $value instanceof \Techart\Core\Call || is_callable($value);
	}

}
