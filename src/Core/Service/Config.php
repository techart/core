<?php

namespace Techart\Core\Service;

use Techart\Core as TAO;

/**
 * Class Config
 * @package Techart\Core\Service
 */
class Config extends \Techart\Core\Service
{
    /**
     * @var array
     */
    protected $values = array();
    /**
     * @var array
     */
    protected $scopes = array();

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->setOption('paths', array('../app/config', '../config'));
        $this->setOption('root_config', '../config/site.php');
    }

    /**
     *
     */
    public function init()
    {
        $file = $this->getOption('root_config');
        if ($file && is_file($file)) {
            $m = include($file);
            if (isset($m['env'])) {
                $this->setOption('env', $m['env']);
            }
        }
    }

    /**
     * @return $this|null
     */
    public function service()
    {
        $args = func_get_args();
        if (count($args) == 0) {
            return $this;
        }
        $name = trim($args[0]);
        $default = isset($args[1]) ? $args[1] : null;
        return $this->get($name, $default);
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function get($name, $default = null)
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }
        $scope = 'site';
        if ($p = strpos($name, ':')) {
            $scope = substr($name, 0, $p);
            $name = substr($name, $p + 1);
        }
        return $this->getFromScope($scope, $name, $default);
    }

    /**
     * @param $scope
     * @return array
     */
    public function loadScope($scope)
    {
        $files = array();
        $env = $this->getOption('env');
        foreach ($this->getOption('paths') as $path) {
            $path = rtrim($path, '/');
            $files[] = "{$path}/{$scope}.php";
            if ($env) {
                $files[] = "{$path}/{$scope}.{$env}.php";
            }
        }

        $values = array();

        foreach ($files as $file) {
            if (is_file($file)) {
                $m = include($file);
                $values = \Techart\Core::mergeArrays($values, $m);
            }
        }

        return $values;
    }

    /**
     * @param $scope
     * @param $name
     * @param null $default
     * @return null
     */
    public function getFromScope($scope, $name, $default = null)
    {
        if (!isset($this->scopes[$scope])) {
            $this->scopes[$scope] = $this->loadScope($scope);
        }

        $values = $this->scopes[$scope];

        while ($m = TAO::regexp('{^([^/]+)/(.+)$}', $name)) {
            $p = trim($m[1]);
            if (isset($values[$p]) && is_array($values[$p])) {
                $name = trim($m[2]);
                $values = $values[$p];
            }
        }

        return isset($values[$name]) ? $values[$name] : $default;
    }
}