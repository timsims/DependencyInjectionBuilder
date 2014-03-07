<?php

/**
 * 
 */
class DependencyInjectionBuilder
{

    protected $_container;

    function __construct(ArrayAccess $container = null)
    {
        $this->_container = $container ? $container : array();
    }

    public function create($class, $input = [])
    {
        if (!isset($this->_container[$class])) {
            $this->_container[$class] = $this->_getInstance($class, $input);
        }

        return $this->_container[$class];
    }

    public function call($controller, $input = [])
    {
        return call_user_func_array($controller, $this->_getDependencies($controller, $input));
    }

    protected function _getInstance($class, $input)
    {
        $metaClass = new ReflectionClass($class);

        return $metaClass->hasMethod('__construct') ?
            $metaClass->newInstanceArgs($this->_getDependencies([$class, '__construct'], $input)) :
            new $class;
    }

    protected function _getDependencies($controller, $input)
    {
        $method = new ReflectionMethod($controller[0], $controller[1]);
        $dependencies = [];
        foreach ($method->getParameters() as $param) {
            $parameterName = $param->getName();

            if (isset($input[$parameterName])) {
                $dependencies[$parameterName] = $input[$parameterName];
            } else {
                if (isset($param->getClass()->name)) {
                    $dependencies[$parameterName] = $this->create($param->getClass()->name);
                } elseif (isset($this->_container[$parameterName])) {
                    $dependencies[$parameterName] = $this->_container[$parameterName];
                }
            }
        }
        return $dependencies;
    }
}