<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Setup_Generic extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Core_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName,
        $moduleName = 'Magento_Widget',
        $connectionName = ''
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }


    /**
     * Get migration instance
     *
     * @param array $data
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function createMigrationSetup(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
