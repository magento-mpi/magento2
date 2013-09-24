<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GoogleShopping_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var null
     */
    protected $_googleShoppingData = null;

    /**
     * Config factory
     *
     * @var Magento_GoogleShopping_Model_ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param Magento_GoogleShopping_Model_ConfigFactory $configFactory
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_GoogleShopping_Helper_Data $googleShoppingData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $modulesConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param $resourceName
     */
    public function __construct(
        Magento_GoogleShopping_Model_ConfigFactory $configFactory,
        Magento_GoogleShopping_Helper_Data $googleShoppingData,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $modulesConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        $resourceName
    ) {
        $this->_configFactory = $configFactory;
        $this->_googleShoppingData = $googleShoppingData;
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $modulesConfig,
            $moduleList, $resource, $modulesReader, $resourceName
        );
    }

    /**
     * @return null
     */
    public function getGoogleShoppingData()
    {
        return $this->_googleShoppingData;
    }
}
