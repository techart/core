<?php

namespace Techart\Core;

/**
 * Обертка над php-функциями для работы со строками
 *
 * <p>Класс включает в себя набор функций для работы со строками. При этом используется явный
 * вызов функций модуля mbstring.</p>
 * <p>Поскольку мы работаем с UTF-8, модуль mbstring нужен практически всегда. Вместе с тем,
 * бывают ситуации, когда необходимо работать со строкой, как с последовательностью
 * байт, а не как с набором юникодных символов. Например, это может понадобиться при
 * обработке бинарных строк (со встроенными функциями для этого у PHP туго).</p>
 * <p>На данный момент принято некрасивое, но работающее решение ввести методы begin_binary() и
 * end_binary(), которые обеспечивают переход модуля mbstring в кодировку ASCII и выход из
 * нее. Соответственно, при необходимости использовать методы класса для обработки бинарных
 * данных соответствующий кусок кода необходимо выделить с помощью вызовов
 * begin_binary()/end_binary(), при этом вызовы могут быть вложенными.</p>
 *
 * @package Core
 */
class Strings
{

    protected static $encodings = array();

    /**
     * Переводит модуль в бинарный режим.
     *
     */
    public static function begin_binary()
    {
        array_push(self::$encodings, mb_internal_encoding());
        mb_internal_encoding('ASCII');
    }

    /**
     * Переводит модуля из бинарного режима в режим использования предыдущей кодировки.
     *
     */
    public static function end_binary()
    {
        if ($encoding = array_pop(self::$encodings)) {
            mb_internal_encoding($encoding);
        }
    }

    /**
     * Объединяет набор строк в одну
     *
     * @return string
     */
    public static function concat()
    {
        $args = func_get_args();
        return implode('', \Techart\Core::normalize_args($args));
    }

    /**
     * Объединяет строки с использованием разделителя
     *
     * @return string
     */
    public static function concat_with()
    {
        $args = \Techart\Core::normalize_args(func_get_args());
        return implode((string)array_shift($args), $args);
    }


    /**
     * Возвращает подстроку
     *
     * @param string $string
     * @param int $start
     * @param int $length
     *
     * @return string
     */
// TODO: eliminate if
    public static function substr($string, $start, $length = null)
    {
        return $length === null ?
            mb_substr($string, $start) :
            mb_substr($string, $start, $length);
    }

    /**
     * Выполняет замену в строке
     *
     * @param string $string
     * @param string $what
     * @param string $with
     *
     * @return string
     */
    public static function replace($string, $what, $with)
    {
        return str_replace($what, $with, $string);
    }

    /**
     * Удаляет пробельные символы в конце строки
     *
     * @param string $tail
     *
     * @return string
     */
    public static function chop($tail)
    {
        return rtrim($tail);
    }

    /**
     * Удаляет пробельные символы в начале и конце строки
     *
     * @param string $string
     * @param string $chars
     *
     * @return string
     */
    public static function trim($string, $chars = null)
    {
        return $chars ? trim($string, $chars) : trim($string);
    }

    /**
     * Разбивает строку по пробелам
     *
     * @param string $string
     *
     * @return array
     */
    public static function split($string)
    {
        return explode(' ', $string);
    }

    /**
     * Разбивает строку по заданному разделителю
     *
     * @param string $delimiter
     * @param string $string
     *
     * @return array
     */
    public static function split_by($delimiter, $string)
    {
        return ($string === '') ? array() : explode($delimiter, $string);
    }

    /**
     * Выполняет форматирование строки
     *
     * @return string
     */
    public static function format()
    {
        $args = func_get_args();
        return vsprintf(array_shift($args), $args);
    }

    /**
     * Проверяет, начинается ли строка с заданной подстроки
     *
     * @param string $string
     * @param string $head
     *
     * @return boolean
     */
    public static function starts_with($string, $head)
    {
        return (mb_strpos($string, $head) === 0);
    }

    /**
     * Проверяет заканчивается ли строка заданной подстрокой
     *
     * @param string $string
     * @param string $tail
     *
     * @return boolean
     */
    public static function ends_with($string, $tail)
    {
        $pos = mb_strrpos($string, $tail);
        if ($pos === false) {
            return false;
        }
        return ((mb_strlen($string) - $pos) == mb_strlen($tail));
    }

    /**
     * Проверяет, содержит ли строка заданную подстроку
     *
     * @param string $string
     * @param string $fragment
     *
     * @return boolean
     */
    public static function contains($string, $fragment)
    {
        return ($fragment && (mb_strpos($string, $fragment) !== false));
    }

    /**
     * Приводит все символы строки к нижнему регистру
     *
     * @param string $string
     *
     * @return string
     */
    public static function downcase($string)
    {
        return mb_strtolower($string);
    }

    /**
     * Приводит все символы строки к верхнему регистру.
     *
     * @param string $string
     *
     * @return string
     */
    public static function upcase($string)
    {
        return mb_strtoupper($string);
    }

    /**
     * Приводит первый символ строки к верхнему регистру
     *
     * @param string $string
     *
     * @return string
     */
    public static function capitalize($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }

    /**
     * Приводит первый символ строки к нижнему регистру
     *
     * @param string $string
     *
     * @return string
     */
    public static function lcfirst($string)
    {
        return mb_strtolower(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }

    /**
     * Аналог ucfirst, работающий с UTF8
     *
     * @param string $string
     *
     * @return string
     */
    public static function capitalize_words($string)
    {
        return preg_replace_callback(
            '{(\s+|^)(.)}u',
            create_function('$m', 'return $m[1].mb_strtoupper(mb_substr($m[2],0,1));'),
            $string
        );
    }

    /**
     * Приводит идентификатор к виду CamelCase
     *
     * @param string $string
     *
     * @return string
     */
    public static function to_camel_case($string, $lcfirst = false)
    {
        $s = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        return $lcfirst ? strtolower(substr($s, 0, 1)) . substr($s, 1) : $s;
    }

    /**
     * Декодирует строку из base64
     *
     * @param string $string
     *
     * @return string
     */
    public static function decode64($string)
    {
        return base64_decode($string);
    }

    /**
     * Кодирует строку в base64
     *
     * @param string $string
     *
     * @return string
     */
    public static function encode64($string)
    {
        return base64_encode($string);
    }

}
