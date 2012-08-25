<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_Base implements Magento_ObjectManager
{
    /**
     * Created instances
     *
     * @var array
     */
    protected $_instances = array();

    /**
     * @var Magento_ObjectManager_Factory[]
     */
    protected $_factories = array();

    /**
     * @var Magento_ObjectManager_Factory[]
     */
    protected $_factoriesByClasses = array();

    /**
     * Retreive object factory for class name
     *
     * @param string $className
     * @return Magento_ObjectManager_Factory
     * @throws OutOfBoundsException
     */
    protected function _getFactoryFor($className)
    {
        if (isset($this->_factoriesByClasses[$className])) {
            return $this->_factoriesByClasses[$className];
        }

        $factoryClassName = $this->_buildFactoryClassName($className);
        if (isset($this->_factories[$factoryClassName])) {
            return $this->_factories[$factoryClassName];
        }

        if (class_exists($factoryClassName)) {
            $factory = new $factoryClassName($this);
            $this->_factoriesByClasses[$className] = $factory;
            $this->_factories[$factoryClassName] = $factory;
            return $factory;
        }

        $parentClassName = get_parent_class($className);
        if ($parentClassName) {
            return $this->_getFactoryFor($parentClassName);
        } else {
            throw new OutOfBoundsException();
        }
    }

    /**
     * @param string $className
     * @return string
     */
    protected function _buildFactoryClassName($className)
    {
        if (substr($className, -8) == 'Abstract') {
            return str_replace('Abstract', 'Factory', $className);
        }
        return $className . 'Factory';
    }

    /**
     * Create new object instance
     *
     * @abstract
     * @param string $objectName
     * @param array $arguments
     * @return mixed
     */
    public function create($objectName, array $arguments = array())
    {
        $className = $this->_config->getModelClassName($objectName);
        try {
            $factory = $this->_getFactoryFor($className);
        } catch (OutOfBoundsException $e) {
            throw new LogicException('Could not create object ' . $className . '. Factory is not defined');
        }
        $factory->createFromArray($arguments, $className);
    }

    /**
     * Retreive cached object instance
     *
     * @abstract
     * @param string $objectName
     * @param array $arguments
     * @return mixed
     */
    public function get($objectName, array $arguments = array())
    {
        if (!isset($this->_instances[$objectName])) {
            $this->_instances[$objectName] = $this->create($objectName, $arguments);
        }
        return $this->_instances[$objectName];
    }
}
