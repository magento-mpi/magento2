<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Checkout Resource Setup Model
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Model_Resource_Setup extends Magento_Eav_Model_Entity_Setup
{
    /**
     * @var Magento_Customer_Helper_Address
     */
    protected $_customerAddress;

    /**
     * @param Magento_Customer_Helper_Address $customerAddress
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param array $moduleConfiguration
     * @param $resourceName
     */
    public function __construct(
        Magento_Customer_Helper_Address $customerAddress,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_CacheInterface $cache,
        array $moduleConfiguration,
        $resourceName
    ) {
        $this->_customerAddress = $customerAddress;
        parent::__construct($logger, $eventManager, $resource, $modulesReader,
            $cache, $moduleConfiguration, $resourceName
        );
    }

    /**
     * @return Magento_Customer_Helper_Address
     */
    public function getCustomerAddress()
    {
        return $this->_customerAddress;
    }
}
