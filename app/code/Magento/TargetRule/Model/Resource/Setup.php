<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Setup Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_TargetRule_Model_Resource_Setup extends Magento_Catalog_Model_Resource_Setup
{
    /**
     * @var Magento_Enterprise_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName,
        $moduleName = 'Magento_TargetRule',
        $connectionName = ''
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct($context, $cache, $resourceName, $moduleName, $connectionName);
    }


    /**
     * Create migration setup
     *
     * @param array $data
     * @return Magento_Enterprise_Model_Resource_Setup_Migration
     */
    public function createMigrationSetup(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
