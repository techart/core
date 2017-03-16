<?php

namespace Techart;

/**
 * Class Core
 * @package Techart
 */
class Core
{
    const PATH_VARIABLE = 'TAO_PATH';

    protected static $start_time = 0;
    protected static $base_dir = null;
    protected static $save_dir = null;

    /**
     *
     */
    protected static function push_dir()
    {
        self::$save_dir = getcwd();
        if (self::$base_dir) {
            chdir(self::$base_dir);
        }
    }

    /**
     *
     */
    protected static function pop_dir()
    {
        chdir(self::$save_dir);
    }

    /**
     * @return int
     */
    public static function start_time()
    {
        return self::$start_time;
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public static function equals($a, $b)
    {
        if ($a instanceof \Techart\Core\EqualityInterface) {
            return $a->equals($b);
        }
        if ($b instanceof \Techart\Core\EqualityInterface) {
            return $b->equals($a);
        }

        if ((($a instanceof \stdClass) && ($b instanceof \stdClass))) {
            $a = (array)clone $a;
            $b = (array)clone $b;
        }

        if ((is_array($a) && is_array($b)) ||
            (($a instanceof \ArrayObject) && ($b instanceof \ArrayObject))
        ) {

            if (count($a) != count($b)) {
                return false;
            }

            foreach ($a as $k => $v)
                if ((isset($a[$k]) && !isset($b[$k])) || !Core::equals($v, $b[$k])) {
                    return false;
                }
            return true;
        }

        return ($a === $b);
    }

    /**
     * Создает объект класса stdClass
     *
     * @param  $values
     *
     * @return \stdClass
     */
    public static function object($values = array())
    {
        $r = new \stdClass();
        foreach ($values as $k => $v)
            $r->$k = $v;
        return $r;
    }

    /**
     * Создает объект класса ArrayObject
     *
     * @param  $values
     *
     * @return \ArrayObject
     */
    public static function hash($values = array())
    {
        return new \ArrayObject((array)$values);
    }

    /**
     * @param        $target
     * @param string $method
     *
     * @return \Techart\Core\Call
     */
    public static function call($target, $method)
    {
        $args = func_get_args();
        return new \Techart\Core\Call(array_shift($args), array_shift($args), $args);
    }

    /**
     * @param callable|string $call
     * @param array $parms
     *
     * @return mixed
     */
    public static function invoke($call, array $parms = array())
    {
        if (is_string($call) && strpos($call, '::') !== false) {
            $parts = explode('::', $call);
            $call = array($parts[0], $parts[1]);
        }
        if ($call instanceof \Techart\Core\InvokeInterface) {
            return $call->invoke($parms);
        }
        return call_user_func_array($call, $parms);
    }

    /**
     * @return bool
     */
    public static function is_cli()
    {
        return php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Обеспечивает возможность построения цепочки вызовов для переданного объекта.
     *
     * @param  $object
     *
     * @return Object
     */
    public static function with($object)
    {
        return $object;
    }

    /**
     * Тоже что и with, только возвращает клон объекта
     *
     * @param  $object
     *
     * @return Object
     */
    public static function with_clone($object)
    {
        return clone $object;
    }

    /**
     * Возвращает элемент индексируемого объекта по его индексу
     *
     * @param  $object
     * @param  $index
     *
     * @return mixed
     */
    public static function with_index($object, $index)
    {
        return $object[$index];
    }

    /**
     * Возвращает значение свойства объекта
     *
     * @param        $object
     * @param string $attr
     *
     * @return mixed
     */
    public static function with_attr($object, $attr)
    {
        return $object->$attr;
    }

    /**
     * Возвращает альтернативу для null-значения
     *
     * @param  $value
     * @param  $alternative
     *
     * @return mixed
     */
    public static function if_null($value, $alternative)
    {
        return $value === null ? $alternative : $value;
    }

    /**
     * Возвращает альтернативу для неистинного значения
     *
     * @param  $value
     * @param  $alternative
     *
     * @return mixed
     */
    public static function if_not($value, $alternative)
    {
        return $value ? $value : $alternative;
    }

    /**
     * Возвращает альтернативу для ложного значения
     *
     * @param  $value
     * @param  $alternative
     *
     * @return mixed
     */
    public static function if_false($value, $alternative)
    {
        return $value === false ? $alternative : $value;
    }

    /**
     * Возвращает альтернативу отсутствующему индексированному значению
     *
     * @param       $values
     * @param mixed $index
     * @param mixed $alternative
     *
     * @return mixed
     */
    public static function if_not_set($values, $index, $alternative)
    {
        return isset($values[$index]) ? $values[$index] : $alternative;
    }

    /**
     * Создает объект заданного класса
     *
     * @param string $class
     *
     * @return object
     */
    public static function make($class)
    {
        $args = func_get_args();
        return self::amake($class, array_slice($args, 1));
    }

    /**
     * Создает объект заданного класса с массивом значений параметров конструктора
     *
     * @param string $class
     * @param array $parms
     *
     * @return object
     */
    public static function amake($class, array $parms)
    {
        $reflection = \Techart\Core\Types::reflection_for($class);
        return $reflection->getConstructor() ?
            $reflection->newInstanceArgs($parms) :
            $reflection->newInstance();
    }

    /**
     * Выполняет нормализацию аргументов
     *
     * @param array $args
     *
     * @return array
     */
    public static function normalize_args(array $args)
    {
        return (count($args) == 1 && isset($args[0]) && is_array($args[0])) ? $args[0] : $args;
    }

    /**
     * Выполняет разбор переменной окружения TAO_PATH
     *
     * @return array
     */
    private static function parse_environment_paths()
    {
        $result = array();
        if (($path_var = getenv(self::PATH_VARIABLE)) !== false) {
            foreach (\Techart\Core\Strings::split_by(';', $path_var) as $rule)
                if ($m = \Techart\Core\Regexps::match_with_results('{^([-A-Za-z0-9*][A-Za-z0-9_.]*):(.+)$}', $rule)) {
                    $result[$m[1]] = $m[2];
                }
        }
        return $result;
    }

}
