<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Resource\Solr;

/**
 * Search engine resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
     * @var string
     * @deprecated after 1.11.2.0
     */
    protected $_advancedIndexFieldsPrefix = '#';

    /**
     * List of static fields for index
     *
     * @var string[]
     * @deprecated after 1.11.2.0
     */
    protected $_advancedStaticIndexFields = array('#visibility');

    /**
     * List of obligatory dynamic fields for index
     *
     * @var string[]
     */
    protected $_advancedDynamicIndexFields = array('#position_category_', '#price_');

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
     * @var \Magento\CatalogSearch\Model\Indexer\Fulltext
     */
    protected $catalogSearchIndexer;

    /**
     * Search coll factory
     *
     * @var \Magento\Search\Model\Resource\CollectionFactory
     */
    protected $_searchCollectionFactory;

    /**
     * Search resource
     *
     * @var \Magento\Search\Model\Resource\Advanced
     */
    protected $_searchResource;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Search\Model\Resource\CollectionFactory $searchCollectionFactory
     * @param \Magento\CatalogSearch\Model\Indexer\Fulltext $catalogSearchIndexer
     * @param \Magento\Search\Model\Resource\Index $searchResourceIndex
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Search\Model\Resource\Advanced $searchResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Search\Model\Factory\Factory $searchFactory
     */
    public function __construct(
        \Magento\Search\Model\Resource\CollectionFactory $searchCollectionFactory,
        \Magento\CatalogSearch\Model\Indexer\Fulltext $catalogSearchIndexer,
        \Magento\Search\Model\Resource\Index $searchResourceIndex,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Search\Model\Resource\Advanced $searchResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Search\Model\Factory\Factory $searchFactory
    ) {
        $this->_searchCollectionFactory = $searchCollectionFactory;
        $this->catalogSearchIndexer = $catalogSearchIndexer;
        $this->_searchResourceIndex = $searchResourceIndex;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_adapter = $searchFactory->getFactory()->createAdapter();
        $this->_searchResource = $searchResource;
        $this->_scopeConfig = $scopeConfig;
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
        $commitMode = $this->_scopeConfig->getValue(
            \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL ||
            $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE;
    }

    /**
     * Check if allow commit action is possible depending on current commit mode
     *
     * @return bool
     */
    protected function _canAllowCommit()
    {
        $commitMode = $this->_scopeConfig->getValue(
            \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL ||
            $commitMode == \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL;
    }

    /**
     * Initialize search engine adapter
     *
     * @return $this
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
     * @param string $query
     * @param array  $params
     * @param string $entityType 'product'|'cms'
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
     * @return $this
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
     * @return $this
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
     * @param int|array|null $storeIds
     * @param int|array|null $entityIds
     * @param string $entityType 'product'|'cms'
     * @return $this
     */
    public function cleanIndex($storeIds = null, $entityIds = null, $entityType = 'product')
    {
        if ($storeIds === array() || $entityIds === array()) {
            return $this;
        }

        if (is_null($storeIds) || $storeIds == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $storeIds = array_keys($this->_storeManager->getStores());
        } else {
            $storeIds = (array)$storeIds;
        }

        $queries = array();
        if (empty($entityIds)) {
            foreach ($storeIds as $storeId) {
                $queries[] = 'store_id:' . $storeId;
            }
        } else {
            $entityIds = (array)$entityIds;
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
        return $this->_searchCollectionFactory->create();
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
     * @return true
     */
    public function allowAdvancedIndex()
    {
        return true;
    }

    /**
     * Retrieve allowed visibility values for current engine
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
     * @param string|null $separator
     * @return array
     */
    public function prepareEntityIndex($index, $separator = null)
    {
        return $index;
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return true
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
     * @return $this
     */
    public function optimizeIndex()
    {
        $this->_adapter->optimize();
        return $this;
    }

    /**
     * Commit search engine index changes
     *
     * @return $this
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
     * @param bool $state
     * @return $this
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

    /**
     * Searchable Attributes
     *
     * @var null
     */
    protected $_searchableAttributes = null;

    /**
     * Store searchable attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function storeSearchableAttributes(array $attributes)
    {
        $this->_adapter->storeSearchableAttributes($attributes);
        return $this;
    }

    /**
     * Retrieve attribute field name for search engine
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute|string $attribute
     * @param string $target
     * @return string|bool
     */
    public function getSearchEngineFieldName($attribute, $target = 'default')
    {
        return $this->_adapter->getSearchEngineFieldName($attribute, $target);
    }

    /**
     * Refresh products indexes affected on category update
     *
     * @param array $productIds
     * @param array $categoryIds
     * @return $this
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        if (!is_array($productIds) || empty($productIds)) {
            $productIds = $this->_searchResourceIndex->getMovedCategoryProductIds($categoryIds[0]);
        }

        if (!empty($productIds)) {
            $this->catalogSearchIndexer->executeList($productIds);
        }

        return $this;
    }

    /**
     * Returns advanced index fields prefix
     *
     * @return string
     * @deprecated after 1.11.2.0
     */
    public function getFieldsPrefix()
    {
        return $this->_advancedIndexFieldsPrefix;
    }

    /**
     * Add to index fields that allowed in advanced index
     *
     * @param array $productData
     * @return array
     * @deprecated after 1.11.2.0
     */
    public function addAllowedAdvancedIndexField($productData)
    {
        $advancedIndex = array();

        foreach ($productData as $field => $value) {
            if (in_array($field, $this->_advancedStaticIndexFields) || $this->_isDynamicField($field)) {
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
     * @param string $field
     * @return bool
     * @deprecated after 1.11.2.0
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
