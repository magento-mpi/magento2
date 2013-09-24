<?php
/**
 * Connection factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ConnectionFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create connection instance by name
     *
     * @param string $instanceName
     * @return Magento_DB_Adapter_Interface
     */
    public function createConnectionInstance($instanceName)
    {
        return $this->_objectManager->get($instanceName);
    }
}
