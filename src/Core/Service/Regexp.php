<?php

namespace Techart\Core\Service;

/**
 * Class Regexp
 * @package Techart\Core\Service
 */
class Regexp extends \Techart\Core\Service
{

    /**
     * @return $this|array|string
     */
    public function service()
    {
        $args = func_get_args();
        if (count($args) == 2) {
            return $this->match($args[0], $args[1]);
        }
        if (count($args) == 3) {
            return $this->replace($args[0], $args[1], $args[2]);
        }
        return $this;
    }

    /**
     * @param $regexp
     * @param $string
     * @return array
     */
    public function match($regexp, $string)
    {
        return \Techart\Core\Regexps::match_with_results($regexp, $string);
    }

    /**
     * @param $regexp
     * @param $replace
     * @param $string
     * @return string
     */
    public function replace($regexp, $replace, $string)
    {
        if (\Techart\Core\Types::is_callable($replace)) {
            return \Techart\Core\Regexps::replace_using_callback($regexp, $replace, $string);
        }
        return \Techart\Core\Regexps::replace($regexp, $replace, $string);
    }
}
