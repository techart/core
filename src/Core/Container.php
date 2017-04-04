<?php

namespace Techart\Core;

use Techart\Core;

/**
 * Class Container
 * @package Techart\Core
 */
class Container
{
    /**
     * @var array
     */
    protected $services = array(
        'config' => array(
            'class' => 'Techart.Core.Service.Config',
            'options' => array(),
        ),
    );

    /**
     * @param $name
     * @param $class
     * @param array $options
     */
    public function add($name, $class, $options = array())
    {
        $this->services[$name] = array(
            'class' => $class,
            'options' => $options,
        );
    }

    /**
     * @param $name
     * @param bool|false $returnData
     * @return bool
     * @throws UndefinedServiceException
     */
    public function has($name, $returnData = false)
    {
        if (!isset($this->services[$name])) {
            $data = $this->get('config')->get("services:{$name}");
            if ($data) {
                if (is_string($data)) {
                    $data = array('class' => $data);
                }
                if (!isset($data['options'])) {
                    $data['options'] = array();
                }
                $this->services[$name] = $data;
            }
        }
        if (isset($this->services[$name])) {
            return $returnData ? $this->services[$name] : true;
        }
        return false;
    }

    /**
     * @param $name
     * @param bool|false $rawObject
     * @return object
     * @throws UndefinedServiceException
     */
    public function get($name, $rawObject = false)
    {
        $data = $this->has($name, true);
        if (!$data) {
            throw new UndefinedServiceException($name);
        }
        if (!isset($data['object'])) {
            $class = $data['class'];
            $service = Core::make($class);
            if ($service instanceof Service) {
                foreach ($data['options'] as $option => $value) {
                    $service->setOption($option, $value);
                }
                $service->init();
            }
            $data['object'] = $service;
            $this->services[$name] = $data;
        }
        $object = $data['object'];
        return ($rawObject || !($object instanceof Service)) ? $object : $object->service();
    }
}