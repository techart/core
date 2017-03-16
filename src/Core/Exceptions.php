<?php

namespace Techart\Core;

class Exception extends \Exception implements PropertyAccessInterface
{

    /**
     * Возвращает значение свойства
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return isset($this->$property) ? $this->$property : null;
    }

    /**
     * Устанавливает значение свойства
     *
     * @param string $property
     * @param        $value
     *
     * @return mixed
     */
    public function __set($property, $value)
    {
        throw isset($this->$property) ?
            new ReadOnlyPropertyException($property) :
            new MissingPropertyException($property);
    }

    /**
     * Проверяет, установлено ли значение свойства
     *
     * @param string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        return isset($this->$property);
    }

    /**
     * Удаляет значение свойства
     *
     * @param string $property
     */
    public function __unset($property)
    {
        throw isset($this->$property) ?
            new ReadOnlyPropertyException($property) :
            new MissingPropertyException($property);
    }

}

/**
 * Базовый класс исключений, связанных с контролем типов
 *
 * @package Core
 */
class TypeException extends Exception
{
}

/**
 * Исключение: нереализованный метод
 *
 * @package Core
 */
class NotImplementedException extends Exception
{
}

/**
 * Исключение: отсутсвует ключ в массиве
 *
 * @package Core
 */
class MissingKeyIntoArrayException extends Exception
{

    protected $arg_array_name;
    protected $arg_key_name;

    /**
     * Конструктор
     *
     * @params string $array_name Имя массива
     * @params string $key_name Имя отсутствующего ключа
     */
    public function __construct($array_name, $key_name)
    {
        $this->arg_array_name = $array_name;
        $this->arg_key_name = $key_name;
        parent::__construct("Missing key '{$this->arg_key_name}' into array '{$this->arg_array_name}'");
    }

}

/**
 * Исключение: некорректный тип аргумента
 *
 * <p>Класс предназначен для случаев проверки типов аргументов методов при невозможности
 * применения статической типизации.</p>
 * <p>Свойства:</p>
 * arg_name
 * имя аргумента
 * arg_type
 * тип аргумента
 *
 * @package Core
 */
class InvalidArgumentTypeException extends TypeException
{

    protected $arg_name;
    protected $arg_type;

    /**
     * Конструктор
     *
     * @param string $name
     * @param        $arg
     */
    public function __construct($name, $arg)
    {
        $this->arg_name = (string)$name;
        $this->arg_type = (string)gettype($arg);
        parent::__construct("Invalid argument type for '$this->arg_name': ($this->arg_type)");
    }

}

/**
 * @package Core
 */
class InvalidArgumentValueException extends Exception
{

    protected $arg_name;
    protected $arg_value;

    /**
     * @param string $name
     * @param        $value
     */
    public function __construct($name, $value)
    {
        $this->arg_name = (string)$name;
        $this->arg_value = $value;
        parent::__construct("Invalid argument value for '$this->arg_name': ($this->arg_value)");
    }

}

/**
 * Базовый класс исключения некорректного доступа к объекту
 *
 * @package Core
 */
class ObjectAccessException extends Exception
{
}

/**
 * Исключение: обращение к несуществующему свойству объекта
 *
 * <p>Исключение должно генерироваться при попытке обращения к несуществующему свойству объекта,
 * как правило при реализации интерфейса Core.PropertyAccessInterface.</p>
 * <p>Свойства:</p>
 * property
 * имя отсутствующего свойства
 *
 * @package Core
 */
class MissingPropertyException extends ObjectAccessException
{

    protected $property;

    /**
     * Конструктор
     *
     * @param string $property
     */
    public function __construct($property)
    {
        $this->property = (string)$property;
        parent::__construct("Missing property: $this->property");
    }

}

/**
 * Исключение: обращение к несуществующему индексу
 *
 * <p>Исключение может генерироваться при обращении к несуществующему индексу объекта,
 * реализующего индексированный доступ (интерфейс Core.IndexedAccessInterface).
 * Альтернативная стратегия -- возврат некоторого значения по умолчанию.</p>
 * index
 * индекс
 *
 * @package Core
 */
class MissingIndexedPropertyException extends ObjectAccessException
{

    protected $index;

    /**
     * Конструктор
     *
     * @param  $index
     */
    public function __construct($index)
    {
        $this->index = (string)$index;
        parent::__construct("Missing indexed property for index $this->index");
    }

}

/**
 * Исключение: вызов несуществующего метода
 *
 * <p>Исключение может генерироваться при попытке вызова отсутствующего метода объекта с
 * помощью динамической диспетчеризации (Core.CallInterface::__call()).</p>
 * method
 * имя метода
 *
 * @package Core
 */
class MissingMethodException extends ObjectAccessException
{

    protected $method;

    /**
     * Конструктор
     *
     * @param string $method
     */
    public function __construct($method)
    {
        $this->method = (string)$method;
        parent::__construct("Missing method: $this->method");
    }

}

/**
 * Исключение: попытка записи read-only свойства
 *
 * <p>Исключение должно генерироваться при попытке записи свойства, доступного только для
 * чтения. В большинстве случаев необходимость в его использовании возникает при реализации
 * интерфейса Core.PropertyAccessInterface</p>
 * <p>Свойства:</p>
 * property
 * имя свойства
 *
 * @package Core
 */
class ReadOnlyPropertyException extends ObjectAccessException
{

    protected $property;

    /**
     * Конструктор
     *
     * @param string $property
     */
    public function __construct($property)
    {
        $this->property = (string)$property;
        parent::__construct("The property is read-only: $this->property");
    }
}

/**
 * Исключение: попытка записи read-only индексного свойства
 *
 * <p>Класс аналогичен Core.ReadOnlyPropertyException, но предназначен для случаев обращения
 * по индексу (интерфейс Core.IndexedAccessInterface).</p>
 * <p>Свойства:</p>
 * index
 * индекс
 *
 * @package Core
 */
class ReadOnlyIndexedPropertyException extends ObjectAccessException
{

    protected $index;

    /**
     * Конструктор
     *
     * @param  $index
     */
    public function __construct($index)
    {
        $this->index = (string)$index;
        parent::__construct("The property is read-only for index: $this->index");
    }

}

/**
 * Класс исключения для объектов доступных только для чтения
 *
 * @package Core
 */
class ReadOnlyObjectException extends ObjectAccessException
{
    protected $object;

    /**
     * Конструктор
     *
     * @param  $object
     */
    public function __construct($object)
    {
        $this->object = $object;
        parent::__construct("Read only object");
    }

}

/**
 * Исключение: попытка удаления свойства объекта
 *
 * <p>Существование этого исключения связано с противоречивой семантикой операции unset()
 * применительно к свойствам объектов. Оригинальная семантика -- удаление
 * public-свойства. В случае обеспечения доступа к свойствам через
 * Core.PropertyAccessInterface возможны две стратегии:</p>
 * <ul><li>присваивание свойству значения null;</li>
 * <li>генерирование исключения класса Core.UndestroayablePropertException.</li>
 * </ul><p>Свойства:</p>
 * property
 * имя свойства
 *
 * @package Core
 */
class UndestroyablePropertyException extends ObjectAccessException
{

    protected $property;

    /**
     * Конструктор
     *
     * @param string $property
     */
    public function __construct($property)
    {
        $this->property = (string)$property;
        parent::__construct("Unable to destroy property: $property");
    }

}

/**
 * Исключение: попытка удаления индексного свойства
 *
 */
class UndestroyableIndexedPropertyException extends ObjectAccessException
{

    /**
     * Название индексного свойства
     *
     * @var string
     */
    protected $property;

    /**
     * Конструктор
     *
     * @param string $property имя свойства
     */
    public function __construct($property)
    {
        $this->property = (string)$property;
        parent::__construct("Unable to destroy indexed property: $property");
    }
}

