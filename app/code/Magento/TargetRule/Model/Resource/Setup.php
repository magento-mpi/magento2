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
     * Construct
     *
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory
     * @param string $resourceName
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_Index_Model_IndexerFactory $indexerFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $resourceMigrationFactory
     * @param Magento_Catalog_Model_Resource_Eav_AttributeFactory $eavAttributeResourceFactory
     * @param Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param string $moduleName
     * @param string $connectionName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory,
        $resourceName,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_Index_Model_IndexerFactory $indexerFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $resourceMigrationFactory,
        Magento_Catalog_Model_Resource_Eav_AttributeFactory $eavAttributeResourceFactory,
        Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $moduleName = 'Magento_TargetRule',
        $connectionName = ''
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct($context, $cache, $attrGrCollFactory, $resourceName, $categoryFactory, $indexerFactory,
            $resourceMigrationFactory, $eavAttributeResourceFactory, $moduleName, $connectionName);
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
