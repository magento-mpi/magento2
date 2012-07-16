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
     * Application config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Created instances
     *
     * @var array
     */
    protected $_instances = array();

    public function __construct($config)
    {
        $this->_config = $config;
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

        switch (count($arguments)) {
            case 1:
                return new $className($arguments[0]);
            case 2:
                return new $className($arguments[0], $arguments[1]);
            case 3:
                return new $className($arguments[0], $arguments[1], $arguments[2]);
            case 4:
                return new $className($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            case 5:
                return new $className($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
        }
        $class = new ReflectionClass($className);
        return $class->newInstanceArgs($class);
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
