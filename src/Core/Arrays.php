<?php

namespace Techart\Core;

/**
 * Набор методов для работы с массивами
 */
class Arrays
{

	/**
	 * Возвращает массив ключей заданного массива
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function keys(array &$array)
	{
		return array_keys($array);
	}

	/**
	 * Выбирает первый элемент массива
	 *
	 * @param array $array
	 *
	 * @return mixed
	 */
	public static function shift(array &$array)
	{
		return array_shift($array);
	}

	/**
	 * Выбирает последний элемент массива
	 *
	 * @param array $array
	 *
	 * @return mixed
	 */
	public static function pop(array &$array)
	{
		return array_pop($array);
	}

	/**
	 * Выбирает из массива значение с заданным ключом
	 *
	 * @param array $array
	 * @param       $key
	 * @param       $default
	 *
	 * @return mixed
	 */
	public static function pick(array &$array, $key, $default = null)
	{
		if (isset($array[$key])) {
			$result = $array[$key];
			unset($array[$key]);
			return $result;
		} else {
			return $default;
		}
	}

	public static function put(array &$array, $value, $position = 0)
	{
		if (is_null($position)) {
			$position = count($array);
		}
		array_splice($array, $position, 0, array($value));
		return $array;
	}

	/**
	 * Изменяет порядок следования элементов в массиве на обратный.
	 *
	 * @param array   $array
	 * @param boolean $preserve_keys
	 *
	 * @return array
	 */
	public static function reverse(array $array, $preserve_keys = false)
	{
		return array_reverse($array, (boolean)$preserve_keys);
	}

	/**
	 * Объединяет массив массивов в единый линейный массив.
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function flatten(array $array)
	{
		$res = array();
		foreach ($array as $item)
			$res = self::merge($res, (array)$item);
		return $res;
	}

	/**
	 * Выполняет пользовательскую функцию над всеми элементами массива.
	 *
	 * @param string $lambda
	 * @param array  $array
	 *
	 * @return array
	 */
// TODO: not only lambda functions
// TODO: $x -> $v
	public static function map($lambda, &$array)
	{
		return array_map(create_function('$x', $lambda), $array);
	}

	/**
	 * Выполняет объединение двух массивов
	 *
	 * @param array $what
	 * @param array $with
	 *
	 * @return array
	 */
	public static function merge(array $what, array $with)
	{
		return array_merge($what, $with);
	}

	/**
	 * Выполняет рекурсивное объединение массивов
	 *
	 * @param array $what
	 * @param array $with
	 *
	 * @return array
	 */
	public static function deep_merge_update(array $what, array $with)
	{
		foreach (array_keys($with) as $k)
			$what[$k] = (isset($what[$k]) && is_array($what[$k]) && is_array($with[$k])) ?
				self::deep_merge_update($what[$k], $with[$k]) : $with[$k];
		return $what;
	}

	/**
	 * Выполняет рекурсивное объединение массивов
	 *
	 * @param array $what
	 * @param array $with
	 *
	 * @return array
	 */
	static function deep_merge_append(array $what, array $with)
	{
		foreach (array_keys($with) as $k) {
			$what[$k] = (isset($what[$k]) && is_array($what[$k]) && is_array($with[$k])) ?
				self::deep_merge_append($what[$k], $with[$k]) :
				(isset($what[$k]) ? array_merge((array)$what[$k], (array)$with[$k]) : $with[$k]);
		}
		return $what;
	}

	/**
	 * Аналог deep_merge_update с передачей основного массива по ссылке
	 *
	 * @param array $what
	 * @param array $with
	 */
	public static function deep_merge_update_inplace(array &$what, array $with)
	{
		foreach (array_keys($with) as $k) {
			if (isset($what[$k]) && is_array($what[$k]) && is_array($with[$k])) {
				self::deep_merge_update_inplace($what[$k], $with[$k]);
			} else {
				$what[$k] = $with[$k];
			}
		}
	}

	/**
	 * Обновление  существующих значений массива из другого массива
	 *
	 * @param array $what
	 * @param array $with
	 */
	public static function update(array &$what, array $with)
	{
		foreach ($with as $k => &$v)
			if (array_key_exists($k, $what)) {
				$what[$k] = $with[$k];
			}
	}

	/**
	 * Дополнение массива значениями, отсутствующими в нем
	 *
	 * @param array $what
	 * @param array $with
	 */
	public static function expand(array &$what, array $with)
	{
		foreach ($with as $k => &$v)
			if (!array_key_exists($k, $what)) {
				$what[$k] = $with[$k];
			}
	}

	/**
	 * Выполняет конкатенацию элементов массива с использованием заданного разделителя
	 *
	 * @param string $delimiter
	 * @param array  $array
	 *
	 * @return string
	 */
	public static function join_with($delimiter, array $array)
	{
		return implode($delimiter, $array);
	}

	/**
	 * Выполняет поиск элемента массива
	 *
	 * @param         $needle
	 * @param array   $heystack
	 * @param boolean $string
	 *
	 * @return mixed
	 */
	public static function search($needle, array &$haystack, $strict = false)
	{
		return array_search($needle, $haystack, (boolean)$strict);
	}

	/**
	 * Проверяет присутствие элемента $value в массиве
	 *
	 * @param array $array
	 * @param       $value
	 *
	 * @return boolean
	 */
	public static function contains(array &$array, $value)
	{
		return array_search($value, $array) !== false;
	}

	/**
	 * Разбивает массив на фиксированное количество частей
	 * 
	 * Возвращает массив с полученными частями массива 
	 * 
	 * @param array $array           исходный массив
	 * @param int   $number_of_parts количество частей
	 *
	 * @return array
	 */
	public static function split($array, $number_of_parts)
	{
		$result = array();
		$total_elements = count($array);
		$in_part = ceil($total_elements / $number_of_parts);
		$offset = 0;
		for ($i = 0; ($i < $number_of_parts) && ($offset < $total_elements); $i++) {
			$offset = $i * $in_part;
			$result[] = array_slice($array, $offset, $in_part);
		}
		return $result;
	}

	//TODO: рфекторинг и вынесение в отдельный модуль
	public static function create_tree($flat, $options = array())
	{
		Core::load('Tree');
		return Tree::create_tree($flat, $options);
	}
}

