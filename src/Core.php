<?php

namespace Techart;

use Techart\Core\ImmutableServiceException;
use Techart\Core\Service;
use Techart\Core\UndefinedServiceException;

/**
 * Class Core
 * @package Techart
 */
class Core
{
    public static $appLibPath = '../app/lib';

    protected static $start_time = 0;
    protected static $base_dir = null;
    protected static $save_dir = null;
    protected static $container = null;

    /**
     *
     */
    public static function init()
    {
        spl_autoload_register(array(self, 'autoloadAppClasses'));
    }

    /**
     * @param $name
     */
    public static function autoLoadAppClasses($name)
    {
        $path = self::$appLibPath;
        if ($name == 'App') {
            include_once("{$path}/App.php");
        } elseif (strpos($name, 'App\\') === 0) {
            $name = str_replace('\\', '/', substr($name, 4));
            include_once("{$path}/{$name}.php");
        }
    }

    /**
     * @return null|object
     */
    public static function container()
    {
        if (is_null(self::$container)) {
            self::$container = self::make('Techart.Core.Container');
        }
        return self::$container;
    }

    /**
     * @param $name
     * @param $class
     * @param array $options
     * @throws ImmutableServiceException
     */
    public static function addService($name, $class, $options = array())
    {
        return self::container()->add($name, $class, $options);
    }

    /**
     * @param $name
     * @param bool|false $rawObject
     * @return mixed
     * @throws UndefinedServiceException
     */
    public static function get($name, $rawObject = false)
    {
        return self::container()->get($name, $rawObject);
    }

    /**
     * @param $name
     * @param bool|false $returnData
     */
    public static function has($name, $returnData = false)
    {
        return self::container()->has($name, $returnData);
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     * @throws UndefinedServiceException
     */
    public static function __callStatic($name, $args)
    {
        $service = self::get($name, true);
        return ($service instanceof Service) ?call_user_func_array(array($service, 'service'), $args) : $service;
    }

    /**
     * @param array $m1
     * @param array $m2
     * @return array
     */
    public static function mergeArrays(array $m1, array $m2)
    {
        foreach ($m2 as $k => $v) {
            if (isset($m1[$k]) && is_array($m1[$k]) && is_array($v)) {
                $m1[$k] = self::mergeArrays($m1[$k], $v);
            } else {
                $m1[$k] = $v;
            }
        }
        return $m1;
    }

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
}
