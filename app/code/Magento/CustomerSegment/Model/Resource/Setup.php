<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CustomerSegment resource setup
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Model_Resource_Setup extends Magento_Eav_Model_Entity_Setup
{
    /**
     * @var Magento_Enterprise_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @var Magento_CustomerSegment_Model_Resource_Segment_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory
     * @param Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_CustomerSegment_Model_Resource_Segment_CollectionFactory $collectionFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory,
        Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_CustomerSegment_Model_Resource_Segment_CollectionFactory $collectionFactory,
        $resourceName,
        $moduleName = 'Magento_CustomerSegment',
        $connectionName = ''
    ) {
        parent::__construct($context, $cache, $attrGrCollFactory, $resourceName, $moduleName, $connectionName);
        $this->_migrationFactory = $migrationFactory;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param array $data
     * @return Magento_Enterprise_Model_Resource_Setup_Migration
     */
    public function createSetupMigration(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }

    /**
     * @return Magento_CustomerSegment_Model_Resource_Segment_Collection
     */
    public function createSegmentCollection()
    {
        return $this->_collectionFactory->create();
    }
}
