<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource Setup Model
 */
class Magento_VersionsCms_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Core_Model_Date
     */
    protected $_coreDate;

    /**
     * @var Magento_Enterprise_Model_Resource_Setup_MigrationFactory
     */
    protected $_entMigrationFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_Date $coreDate
     * @param Magento_Enterprise_Model_Resource_Setup_MigrationFactory $entMigrationFactory
     * @param string $resourceName
     * @param $moduleName
     * @param string $connectionName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_Date $coreDate,
        Magento_Enterprise_Model_Resource_Setup_MigrationFactory $entMigrationFactory,
        $resourceName,
        $moduleName = 'Magento_VersionsCms',
        $connectionName = ''
    ) {
        $this->_coreDate = $coreDate;
        $this->_entMigrationFactory = $entMigrationFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }
}
