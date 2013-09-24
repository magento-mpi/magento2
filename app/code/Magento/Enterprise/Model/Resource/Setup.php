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
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Cms_Model_BlockFactory $modelBlockFactory
     * @param array $moduleConfiguration
     * @param string $resourceName
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Cms_Model_BlockFactory $modelBlockFactory,
        array $moduleConfiguration,
        $resourceName
    ) {
        $this->_modelBlockFactory = $modelBlockFactory;
        parent::__construct($logger, $eventManager, $resource, $modulesReader, $moduleConfiguration, $resourceName);
    }
}
