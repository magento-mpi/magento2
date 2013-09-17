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
    /**
     * @var Magento_Core_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Helper_Data $helper
     * @param $resourceName
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Filesystem $filesystem,
        Magento_Core_Helper_Data $helper,
        $resourceName,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        array $data = array()
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct(
            $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader, $filesystem,
            $helper, $resourceName, $data
        );
    }

    /**
     * @return Magento_Widget_Model_Resource_Setup
     */
    public function getMigrationModel()
    {
        return $this->_migrationFactory->create(array(
            'resourceName' => 'core_setup'
        ));
    }

}
