<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter resource setup
 */
class Magento_Newsletter_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * Resource setup model
     *
     * @var Magento_Core_Model_Resource_Setup_Migration
     */
    protected $_setupMigration;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $setupMigrationFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_Resource_Setup_MigrationFactory $setupMigrationFactory,
        $resourceName,
        $moduleName = 'Magento_Newsletter',
        $connectionName = ''
    ) {
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
        $this->_setupMigration = $setupMigrationFactory->create(array('resourceName' => 'core_setup'));
    }


    /**
     * Get block factory
     *
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function getSetupMigration()
    {
        return $this->_setupMigration;
    }
}
