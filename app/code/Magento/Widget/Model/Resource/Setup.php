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
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Model_Resource_Setup_Context $context,
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
     * @param $data
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function getMigrationInstance($data)
    {
        return $this->_migrationFactory->create($data);
    }
}
