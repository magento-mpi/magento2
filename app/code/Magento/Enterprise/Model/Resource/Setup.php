<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise resource setup
 */
class Magento_Enterprise_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * Block model factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_modelBlockFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param $resourceName
     * @param Magento_Cms_Model_BlockFactory $modelBlockFactory
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
        Magento_Cms_Model_BlockFactory $modelBlockFactory
    ) {
        parent::__construct($logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource,
            $modulesReader, $resourceName);

        $this->_modelBlockFactory = $modelBlockFactory;
    }
}
