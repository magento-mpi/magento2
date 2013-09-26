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
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Customer_Helper_Address $customerAddress
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $modulesConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Resource $resourceResource
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory
     * @param Magento_Core_Model_Theme_CollectionFactory $themeFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param $resourceName
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Customer_Helper_Address $customerAddress,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $modulesConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Resource $resourceResource,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory,
        Magento_Core_Model_Theme_CollectionFactory $themeFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName
    ) {
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $modulesConfig, $moduleList, $resource, $modulesReader,
            $cache, $resourceResource, $themeResourceFactory, $themeFactory, $migrationFactory, $resourceName
        );
        $this->_customerAddress = $customerAddress;
    }

    /**
     * @return Magento_Customer_Helper_Address
     */
    public function getCustomerAddress()
    {
        return $this->_customerAddress;
    }
}
