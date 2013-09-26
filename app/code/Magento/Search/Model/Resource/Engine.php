<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search engine resource model
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Model\Resource;

class Engine implements \Magento\CatalogSearch\Model\Resource\EngineInterface
{
    /**
     * Store search engine adapter model instance
     *
     * @var \Magento\Search\Model\Adapter\AbstractAdapter
     */
    protected $_adapter = null;

    /**
     * Advanced index fields prefix
     *
     * @deprecated after 1.11.2.0
     *
     * @var string
     */
    protected $_advancedIndexFieldsPrefix = '#';

    /**
     * List of static fields for index
     *
     * @deprecated after 1.11.2.0
     *
     * @var array
     */
    protected $_advancedStaticIndexFields = array('#visibility');

    /**
     * List of obligatory dynamic fields for index
     *
     * @deprecated after 1.11.2.0
     *
     * @var array
     */
    protected $_advancedDynamicIndexFields = array(
        '#position_category_',
        '#price_'
    );

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Search resource index
     *
     * @var \Magento\Search\Model\Resource\Index
     */
    protected $_searchResourceIndex;

    /**
     * Catalog search resource fulltext
     *
     * @var \Magento\CatalogSearch\Model\Resource\Fulltext
     */
    protected $_catalogSearchResourceFulltext;

    /**
     * Search coll factory
     *
     * @var \Magento\Search\Model\Resource\CollectionFactory
     */
    protected $_searchCollFactory;

    /**
     * @var \Magento\Search\Model\Resource\Advanced
     */
    protected $_searchResource;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     * 
     * @param \Magento\Search\Model\Resource\CollectionFactory $searchCollFactory
     * @param \Magento\CatalogSearch\Model\Resource\Fulltext $catalogSearchResourceFulltext
     * @param \Magento\Search\Model\Resource\Index $searchResourceIndex
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Search\Model\AdapterInterface $adapter
     * @param \Magento\Search\Model\Resource\Advanced $searchResource
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Search\Model\Resource\CollectionFactory $searchCollFactory,
        \Magento\CatalogSearch\Model\Resource\Fulltext $catalogSearchResourceFulltext,
        \Magento\Search\Model\Resource\Index $searchResourceIndex,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Search\Model\AdapterInterface $adapter,
        \Magento\Search\Model\Resource\Advanced $searchResource,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_searchCollFactory = $searchCollFactory;
        $this->_catalogSearchResourceFulltext = $catalogSearchResourceFulltext;
        $this->_searchResourceIndex = $searchResourceIndex;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_adapter = $adapter;
        $this->_searchResource = $searchResource;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        $this->_initAdapter();
    }

    /**
     * Check if hold commit action is possible depending on current commit mode
     *
     * @return bool
     */
    protected function _canHoldCommit()
    {
        $commitMode = $this->_coreStoreConfig->getConfig(
            \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_XML_PATH
        );

        return $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL
            || $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE;
    }

    /**
     * Check if allow commit action is possible depending on current commit mode
     *
     * @return bool
     */
    protected function _canAllowCommit()
    {
        $commitMode = $this->_coreStoreConfig->getConfig(
            \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_XML_PATH
        );

        return $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL
            || $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL;
    }

    /**
     * Initialize search engine adapter
     *
     * @return \Magento\Search\Model\Resource\Engine
     */
    protected function _initAdapter()
    {
        $this->_adapter->setAdvancedIndexFieldPrefix($this->getFieldsPrefix());
        if (!$this->_canAllowCommit()) {
            $this->_adapter->holdCommit();
        }

        return $this;
    }

    /**
     * Retrieve search resource model
     *
     * @return \Magento\Search\Model\Resource\Advanced
     */
    public function getResource()
    {
        return $this->_searchResource;
    }

    /**
     * Retrieve search resource model
     *
     * @return null
     */
    public function getResourceCollection()
    {
        return null;
    }

    /**
     * Retrieve found document ids search index sorted by relevance
     *
     * @param string $query
     * @param array  $params see description in appropriate search adapter
     * @param string $entityType 'product'|'cms'
     * @return array
     */
    public function getIdsByQuery($query, $params = array(), $entityType = 'product')
    {
        return $this->_adapter->getIdsByQuery($query, $params);
    }

    /**
     * Retrieve results for search request
     *
     * @param  string $query
     * @param  array  $params
     * @param  string $entityType 'product'|'cms'
     * @return array
     */
    public function getResultForRequest($query, $params = array(), $entityType = 'product')
    {
        return $this->_adapter->search($query, $params);
    }

    /**
     * Get stat info using engine search stats component
     *
     * @param  string $query
     * @param  array  $params
     * @param  string $entityType 'product'|'cms'
     * @return array
     */
    public function getStats($query, $params = array(), $entityType = 'product')
    {
        return $this->_adapter->getStats($query, $params);
    }

    /**
     * Add entity data to search index
     *
     * @param int $entityId
     * @param int $storeId
     * @param array $index
     * @param string $entityType 'product'|'cms'
     *
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function saveEntityIndex($entityId, $storeId, $index, $entityType = 'product')
    {
        return $this->saveEntityIndexes($storeId, array($entityId => $index), $entityType);
    }

    /**
     * Add entities data to search index
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entityType 'product'|'cms'
     *
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entityType = 'product')
    {
        $docs = $this->_adapter->prepareDocsPerStore($entityIndexes, $storeId);
        $this->_adapter->addDocs($docs);

        return $this;
    }

    /**
     * Remove entity data from search index
     *
     * For deletion of all documents parameters should be null. Empty array will do nothing.
     *
     * @param  int|array|null $storeIds
     * @param  int|array|null $entityIds
     * @param  string $entityType 'product'|'cms'
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function cleanIndex($storeIds = null, $entityIds = null, $entityType = 'product')
    {
        if ($storeIds === array() || $entityIds === array()) {
            return $this;
        }

        if (is_null($storeIds) || $storeIds == \Magento\Core\Model\AppInterface::ADMIN_STORE_ID) {
            $storeIds = array_keys($this->_storeManager->getStores());
        } else {
            $storeIds = (array) $storeIds;
        }

        $queries = array();
        if (empty($entityIds)) {
            foreach ($storeIds as $storeId) {
                $queries[] = 'store_id:' . $storeId;
            }
        } else {
            $entityIds = (array) $entityIds;
            $uniqueKey = $this->_adapter->getUniqueKey();
            foreach ($storeIds as $storeId) {
                foreach ($entityIds as $entityId) {
                    $queries[] = $uniqueKey . ':' . $entityId . '|' . $storeId;
                }
            }
        }

        $this->_adapter->deleteDocs(array(), $queries);

        return $this;
    }

    /**
     * Retrieve last query number of found results
     *
     * @return int
     */
    public function getLastNumFound()
    {
        return $this->_adapter->getLastNumFound();
    }

    /**
     * Retrieve search result data collection
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function getResultCollection()
    {
        return $this->_searchCollFactory->create()->setEngine($this);
    }

    /**
     * Retrieve advanced search result data collection
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function getAdvancedResultCollection()
    {
        return $this->getResultCollection();
    }

    /**
     * Define if current search engine supports advanced index
     *
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return true;
    }

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @see
     *
     * @return array
     */
    public function getAllowedVisibility()
    {
        return $this->_catalogProductVisibility->getVisibleInSiteIds();
    }

    /**
     * Prepare index array
     *
     * @param array $index
     * @param string $separator
     * @return array
     */
    public function prepareEntityIndex($index, $separator = null)
    {
        return $index;
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
     * Define if selected adapter is available
     *
     * @return bool
     */
    public function test()
    {
        return $this->_adapter->ping();
    }

    /**
     * Optimize search engine index
     *
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function optimizeIndex()
    {
        $this->_adapter->optimize();
        return $this;
    }

    /**
     * Commit search engine index changes
     *
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function commitChanges()
    {
        $this->_adapter->commit();
        return $this;
    }

    /**
     * Hold commit of changes for adapter.
     * Can be used for one time commit after full indexation finish.
     *
     * @return bool
     */
    public function holdCommit()
    {
        if ($this->_canHoldCommit()) {
            $this->_adapter->holdCommit();
            return true;
        }

        return false;
    }

    /**
     * Allow commit of changes for adapter
     *
     * @return bool
     */
    public function allowCommit()
    {
        if ($this->_canAllowCommit()) {
            $this->_adapter->allowCommit();
            return true;
        }

        return false;
    }

    /**
     * Define if third party search engine index needs optimization
     *
     * @param  bool $state
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function setIndexNeedsOptimization($state = true)
    {
        $this->_adapter->setIndexNeedsOptimization($state);
        return $this;
    }

    /**
     * Check if third party search engine index needs optimization
     *
     * @return bool
     */
    public function getIndexNeedsOptimization()
    {
        return $this->_adapter->getIndexNeedsOptimization();
    }

    protected $_searchableAttributes = null;

    /**
     * Store searchable attributes
     *
     * @param array $attributes
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function storeSearchableAttributes(array $attributes)
    {
        $this->_adapter->storeSearchableAttributes($attributes);
        return $this;
    }

    /**
     * Retrieve attribute field name for search engine
     *
     * @param   $attribute
     * @param   string $target
     *
     * @return  string|bool
     */
    public function getSearchEngineFieldName($attribute, $target = 'default')
    {
        return $this->_adapter->getSearchEngineFieldName($attribute, $target);
    }

    /**
     * Refresh products indexes affected on category update
     *
     * @param  array $productIds
     * @param  array $categoryIds
     * @return \Magento\Search\Model\Resource\Engine
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        if (!is_array($productIds) || empty($productIds)) {
            $productIds = $this->_searchResourceIndex->getMovedCategoryProductIds($categoryIds[0]);
        }

        if (!empty($productIds)) {
            $this->_catalogSearchResourceFulltext->rebuildIndex(null, $productIds);
        }

        return $this;
    }

    /**
     * Returns advanced index fields prefix
     *
     * @deprecated after 1.11.2.0
     *
     * @return string
     */
    public function getFieldsPrefix()
    {
        return $this->_advancedIndexFieldsPrefix;
    }

    /**
     * Prepare advanced index for products
     *
     * @deprecated after 1.11.2.0
     *
     * @see \Magento\CatalogSearch\Model\Resource\Fulltext->_getSearchableProducts()
     *
     * @param array $index
     * @param int $storeId
     * @param array | null $productIds
     *
     * @return array
     */
    public function addAdvancedIndex($index, $storeId, $productIds = null)
    {
        return $this->_searchResourceIndex->addAdvancedIndex($index, $storeId, $productIds);
    }

    /**
     * Add to index fields that allowed in advanced index
     *
     * @deprecated after 1.11.2.0
     *
     * @param array $productData
     *
     * @return array
     */
    public function addAllowedAdvancedIndexField($productData)
    {
        $advancedIndex = array();

        foreach ($productData as $field => $value) {
            if (in_array($field, $this->_advancedStaticIndexFields)
                || $this->_isDynamicField($field)
            ) {
                if (!empty($value)) {
                    $advancedIndex[$field] = $value;
                }
            }
        }

        return $advancedIndex;
    }

    /**
     * Define if field is dynamic index field
     *
     * @deprecated after 1.11.2.0
     *
     * @param string $field
     *
     * @return bool
     */
    protected function _isDynamicField($field)
    {
        foreach ($this->_advancedDynamicIndexFields as $dynamicField) {
            $length = strlen($dynamicField);
            if (substr($field, 0, $length) == $dynamicField) {
                return true;
            }
        }

        return false;
    }
}
