<?php

use Zend\Di\Di;

class Magento_ObjectManager_Zend extends Magento_ObjectManager_ObjectManagerAbstract
{
    /**
     * @var \Zend\Di\Di
     */
    protected $_di;

    public function __construct($config, \Zend\Di\Di $di)
    {
        $this->_di = $di;
        parent::__construct($config);
    }

    /**
     * Create new object instance
     *
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function create($className, array $arguments = array())
    {
        $className = $this->_config->getModelClassName($className);
        return $this->_di->newInstance($className, array('data' => $arguments));
    }

    /**
     * Retreive cached object instance
     *
     * @param string $objectName
     * @param array $arguments
     * @return mixed
     */
    public function get($className, array $arguments = array())
    {
        $className = $this->_config->getModelClassName($className);
        return $this->_di->get($className, array('data' => $arguments));
    }

}
