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
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    protected $_instances;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Create new object instance
     *
     * @abstract
     * @param $objectName
     * @param array $arguments
     * @return mixed
     */
    public function create($objectName, $arguments = array())
    {
        $className = $this->_config->getModelClassName($objectName);
        return new $className($arguments);
    }

    /**
     * Retreive cached object instance
     *
     * @abstract
     * @param $objectName
     * @param $arguments
     * @return mixed
     */
    public function get($objectName, $arguments)
    {
        if (!isset($this->_instances[$objectName])) {
            $this->_instances[$objectName] = $this->create($objectName, $arguments);
        }
        return $this->_instances[$objectName];
    }
}
