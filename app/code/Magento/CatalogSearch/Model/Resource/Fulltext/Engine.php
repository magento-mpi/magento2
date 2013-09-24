<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogSearch Fulltext Index Engine resource model
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Resource_Fulltext_Engine extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData = null;

    /**
     * Catalog product visibility
     *
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Fulltext collection factory
     *
     * @var Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory
     */
    protected $_fulltextCollectionFactory;

    /**
     * Advanced collection factory
     *
     * @var Magento_CatalogSearch_Model_Resource_Advanced_CollectionFactory
     */
    protected $_advancedCollectionFactory;

    /**
     * CatalogSearch resource helper
     *
     * @var Magento_CatalogSearch_Model_Resource_Helper_Mysql4
     */
    protected $_resourceHelper;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_CatalogSearch_Model_Resource_Advanced_CollectionFactory $advancedCollectionFactory
     * @param Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory $fulltextCollectionFactory
     * @param Magento_Catalog_Model_Product_Visibility $catalogProductVisibility
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_CatalogSearch_Model_Resource_Helper_Mysql4 $resourceHelper
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_CatalogSearch_Model_Resource_Advanced_CollectionFactory $advancedCollectionFactory,
        Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory $fulltextCollectionFactory,
        Magento_Catalog_Model_Product_Visibility $catalogProductVisibility,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_CatalogSearch_Model_Resource_Helper_Mysql4 $resourceHelper
    ) {
        $this->_advancedCollectionFactory = $advancedCollectionFactory;
        $this->_fulltextCollectionFactory = $fulltextCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogSearchData = $catalogSearchData;
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($resource);
    }

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch_fulltext', 'product_id');
    }

    /**
     * Add entity data to fulltext search table
     *
     * @param int $entityId
     * @param int $storeId
     * @param array $index
     * @param string $entity 'product'|'cms'
     * @return Magento_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    public function saveEntityIndex($entityId, $storeId, $index, $entity = 'product')
    {
        $this->_getWriteAdapter()->insert($this->getMainTable(), array(
            'product_id'    => $entityId,
            'store_id'      => $storeId,
            'data_index'    => $index
        ));
        return $this;
    }

    /**
     * Multi add entities data to fulltext search table
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entity 'product'|'cms'
     * @return Magento_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product')
    {
        $data    = array();
        $storeId = (int)$storeId;
        foreach ($entityIndexes as $entityId => $index) {
            $data[] = array(
                'product_id'    => (int)$entityId,
                'store_id'      => $storeId,
                'data_index'    => $index
            );
        }

        if ($data) {
            $this->_resourceHelper->insertOnDuplicate($this->getMainTable(), $data, array('data_index'));
        }

        return $this;
    }

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @return array
     */
    public function getAllowedVisibility()
    {
        return $this->_catalogProductVisibility->getVisibleInSearchIds();
    }

    /**
     * Define if current search engine supports advanced index
     *
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return false;
    }

    /**
     * Remove entity data from fulltext search table
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entity 'product'|'cms'
     * @return Magento_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    public function cleanIndex($storeId = null, $entityId = null, $entity = 'product')
    {
        $where = array();

        if (!is_null($storeId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }
        if (!is_null($entityId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('product_id IN (?)', $entityId);
        }

        // Delete locks reading queries and causes performance issues
        // Insert into index goes with ON_DUPLICATE options.
        // Insert into catalogsearch_result goes with catalog_product_entity inner join
        //$this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Prepare index array as a string glued by separator
     *
     * @param array $index
     * @param string $separator
     * @return string
     */
    public function prepareEntityIndex($index, $separator = ' ')
    {
        return $this->_catalogSearchData->prepareIndexdata($index, $separator);
    }

    /**
     * Return resource name for the full text search
     *
     * @return null
     */
    public function getResourceName()
    {
        return 'Magento_CatalogSearch_Model_Resource_Advanced';
    }

    /**
     * Retrieve fulltext search result data collection
     *
     * @return Magento_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function getResultCollection()
    {
        return $this->_fulltextCollectionFactory->create();
    }

    /**
     * Retrieve advanced search result data collection
     *
     * @return Magento_CatalogSearch_Model_Resource_Advanced_Collection
     */
    public function getAdvancedResultCollection()
    {
        return $this->_advancedCollectionFactory->create();
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLayeredNavigationAllowed()
    {
        return true;
    }

    /**
     * Define if engine is available
     *
     * @return bool
     */
    public function test()
    {
        return true;
    }
}
