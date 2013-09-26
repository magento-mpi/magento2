<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource Setup Model
 */
class Magento_Reports_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param $resourceName
     * @param Magento_Cms_Model_PageFactory $pageFactory
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        $resourceName,
        Magento_Cms_Model_PageFactory $pageFactory
    ) {
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader, $resourceName
        );
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return Magento_Cms_Model_Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
