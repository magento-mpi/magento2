<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Setup model
 */
class Magento_Widget_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Core_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param array $moduleConfiguration
     * @param string $resourceName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        array $moduleConfiguration,
        $resourceName
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct($logger, $eventManager, $resource, $modulesReader, $moduleConfiguration, $resourceName);
    }

    /**
     * Get migration instance
     *
     * @param $data
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function getMigrationInstance($data)
    {
        return $this->_migrationFactory->create($data);
    }
}
