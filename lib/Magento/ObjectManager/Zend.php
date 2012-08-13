<?php

use Zend\Di\Di;

class Magento_ObjectManager_Zend extends Magento_ObjectManager_ObjectManagerAbstract
{
    /**
     * @var \Zend\Di\Di
     */
    protected $_di;

    /**
     * @var string
     */
    protected $_compileDir;

    /**
     * @var string
     */
    protected $_moduleDir;

    /**
     * @param Zend\Di\Di $di
     */
    public function __construct(\Zend\Di\Di $di)
    {
        $this->_di = $di;
        $di->instanceManager()->addSharedInstance($this, "Magento_ObjectManager");
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
        $ni =  $this->_di->newInstance($className, $arguments);
        return $ni;
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
        $ni = $this->_di->get($className, $arguments);
        return $ni;
    }

    /**
     * @param string $class
     * @param array $parameters
     */
    public function setParameters($class, array $parameters)
    {
        $this->_di->instanceManager()->setParameters($class, $parameters);
    }
}
