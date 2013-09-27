<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Setup Resource Model
 */
class Magento_Tax_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_Catalog_Model_Resource_SetupFactory
     */
    protected $_setupFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Catalog_Model_Resource_SetupFactory $setupFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Catalog_Model_Resource_SetupFactory $setupFactory,
        $resourceName,
        $moduleName = 'Magento_Tax',
        $connectionName = ''
    )
    {
        $this->_setupFactory = $setupFactory;
        parent::__construct($context, $config, $cache, $migrationFactory, $coreData,
            $resourceName, $moduleName, $connectionName
        );
    }

    /**
     * Load Tax Table Data
     *
     * @param string $table
     * @return array
     */
    protected function _loadTableData($table)
    {
        $table = $this->getTable($table);
        $select = $this->_connection->select();
        $select->from($table);
        return $this->_connection->fetchAll($select);
    }

    /**
     * @param array $data
     * @return Magento_Catalog_Model_Resource_Setup
     */
    public function getCatalogResourceSetup(array $data = array())
    {
        return $this->_setupFactory->create($data);
    }
}
