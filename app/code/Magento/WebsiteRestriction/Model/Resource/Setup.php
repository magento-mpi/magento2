<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Private sales setup model resource
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_WebsiteRestriction_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
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
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param $resourceName
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Cms_Model_PageFactory $pageFactory,
        $resourceName
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader, $resourceName
        );
    }

    /**
     * @return Magento_Cms_Model_Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
