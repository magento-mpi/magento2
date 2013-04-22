<?php
/**
 * Factory of web API action controllers (resources).
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Mage_Core_Service_Config */
    protected $_config;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Service_Config $config
     */
    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Service_Config $config)
    {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Create service instance.
     *
     * @param string $servicereferenceId
     * @return object
     */
    public function createServiceInstance($servicereferenceId)
    {
        $className = $this->_config->getServiceClassByServiceName($servicereferenceId);
        return $this->_objectManager->create($className);
    }

    /**
     * Create service helper instance.
     *
     * @param string $serviceHelperClassRef
     * @return object
     */
    public function createServiceHelperInstance($serviceHelperClassRef)
    {
        return $this->_objectManager->create($serviceHelperClassRef);
    }

    /**
     * Create an instance.
     *
     * @param string $classRef
     * @return object
     */
    public function createObjectInstance($classRef)
    {
        return $this->_objectManager->create($classRef);
    }
}
