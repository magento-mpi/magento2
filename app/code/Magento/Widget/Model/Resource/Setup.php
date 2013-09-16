<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Widget_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup_Migration
{
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Filesystem $filesystem,
        Magento_Core_Helper_Data $helper,
        array $data = array()
    ) {
        $resourceName = 'core_setup';
        parent::__construct(
            $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader, $filesystem, $helper,
            $resourceName, $data
        );
    }

}
