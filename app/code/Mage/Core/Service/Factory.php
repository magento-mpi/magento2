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
    public function __construct(Magento_ObjectManager $objectManager/*, Mage_Core_Service_Config $config*/)
    {
        $this->_objectManager = $objectManager;
        /*$this->_config = $config;*/
    }

    /**
     * Create service instance.
     *
     * @param string $serviceName
     * @return object
     */
    public function createServiceInstance($className)
    {
        /*$className = $this->_config->getServiceClassByServiceName($className);*/
        return $this->_objectManager->create($className);
    }
}
