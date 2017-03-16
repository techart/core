<?php

namespace Techart\Core;


/**
 * Класс обертка над встроенными функциями работы с регулярными выражениями
 *
 * <p>Класс группирует функции для работы с регулярными выражениями в отдельное пространство
 * имен (исключительно из эстетических соображений), а также делает работу с некоторыми
 * функциями более удобной.</p>
 *
 */
class Regexps
{

    /**
     * Сопоставляет строку с регулярным выражением
     *
     * @param string $regexp
     * @param string $string
     *
     * @return boolean
     */
    public static function match($regexp, $string)
    {
        return (boolean)preg_match($regexp, $string);
    }

    /**
     * Сопоставляет строку с регулярным выражением, возвращает результат сопоставления
     *
     * @param string $regexp
     * @param string $string
     *
     * @return array
     */
    public static function match_with_results($regexp, $string)
    {
        $m = array();
        return preg_match($regexp, $string, $m) ? $m : false;
    }

    /**
     * Сопоставляет строку с регулярным выражением, возвращает все результаты сопоставления
     *
     * @param string $regexp
     * @param string $string
     * @param int $type
     *
     * @return array
     */
    public static function match_all($regexp, $string, $type = PREG_PATTERN_ORDER)
    {
        $m = array();
        return preg_match_all($regexp, $string, $m, (int)$type) ? $m : false;
    }

    /**
     * Выполняет квотинг строки для использования в качестве регулярного выражения
     *
     * @param string $string
     *
     * @return string
     */
    public static function quote($string)
    {
        return preg_quote($string);
    }

    /**
     * Выполняет замену строк по регулярному выражению
     *
     * @param string $regexp
     * @param string $replacement
     * @param string $source
     * @param int $limit
     *
     * @return string
     */
    public static function replace($regexp, $replacement, $source, $limit = -1)
    {
        return preg_replace($regexp, $replacement, $source, (int)$limit);
    }

    /**
     * Выполняет замену по регулярному выражению с использованием пользовательской функции
     *
     * @param string $regexp
     * @param callback $callback
     * @param string $source
     * @param int $limit
     *
     * @return string
     */
    public static function replace_using_callback($regexp, $callback, $source, $limit = -1)
    {
        return preg_replace_callback($regexp, $callback, $source, $limit);
    }

    /**
     * Выполняет замену по регулярном выражению, возвращает количество замен
     *
     * @param string $regexp
     * @param string $replacement
     * @param string $source
     * @param int $limit
     *
     * @return int
     */
    public static function replace_ref($regexp, $replacement, &$source, $limit = -1)
    {
        $count = 0;
        $source = preg_replace($regexp, $replacement, $source, (int)$limit, $count);
        return $count;
    }

    /**
     * Разбивает строку на подстроки по регулярному выражению
     *
     * @param string $regexp
     * @param string $string
     * @param number $limit
     * @param number $flags
     *
     * @return array
     */
    public static function split_by($regexp, $string, $limit = -1, $flags = 0)
    {
        return preg_split($regexp, $string, (int)$limit, (int)$flags);
    }

}
