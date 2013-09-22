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
 * Enterprise search collection resource model
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Search\Model\Resource;

class Collection
    extends \Magento\Catalog\Model\Resource\Product\Collection
{

    /**
     * Store search query text
     *
     * @var string
     */
    protected $_searchQueryText = '';

    /**
     * Store search query params
     *
     * @var array
     */
    protected $_searchQueryParams = array();

    /**
     * Store search query filters
     *
     * @var array
     */
    protected $_searchQueryFilters = array();

    /**
     * Store found entities ids
     *
     * @var array
     */
    protected $_searchedEntityIds = array();

    /**
     * Store found suggestions
     *
     * @var array
     */
    protected $_searchedSuggestions = array();

    /**
     * Store engine instance
     *
     * @var \Magento\Search\Model\Resource\Engine
     */
    protected $_engine = null;

    /**
     * Store sort orders
     *
     * @var array
     */
    protected $_sortBy = array();

    /**
     * General default query *:* to disable query limitation
     *
     * @var array
     */
    protected $_generalDefaultQuery = array('*' => '*');

    /**
     * Flag that defines if faceted data needs to be loaded
     *
     * @var bool
     */
    protected $_facetedDataIsLoaded = false;

    /**
     * Faceted search result data
     *
     * @var array
     */
    protected $_facetedData = array();

    /**
     * Suggestions search result data
     *
     * @var array
     */
    protected $_suggestionsData = array();

    /**
     * Conditions for faceted search
     *
     * @var array
     */
    protected $_facetedConditions = array();

    /**
     * Stores original page size, because _pageSize will be unset at _beforeLoad()
     * to disable limitation for collection at load with parent method
     *
     * @var int|bool
     */
    protected $_storedPageSize = false;

    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_EntityFactory $entityFactory
     */
    public function __construct(
        \Magento\Search\Helper\Data $searchData,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\Product\Flat $catalogProductFlat,
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_EntityFactory $entityFactory
    ) {
        $this->_searchData = $searchData;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct(
            $catalogData, $catalogProductFlat, $eventManager, $logger, $fetchStrategy, $coreStoreConfig, $entityFactory
        );
    }

    /**
     * Load faceted data if not loaded
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function loadFacetedData()
    {
        if (empty($this->_facetedConditions)) {
            $this->_facetedData = array();
            return $this;
        }

        list($query, $params) = $this->_prepareBaseParams();
        $params['solr_params']['facet'] = 'on';
        $params['facet'] = $this->_facetedConditions;

        $result = $this->_engine->getResultForRequest($query, $params);
        $this->_facetedData = $result['faceted_data'];
        $this->_facetedDataIsLoaded = true;

        return $this;
    }

    /**
     * Return field faceted data from faceted search result
     *
     * @param string $field
     *
     * @return array
     */
    public function getFacetedData($field)
    {
        if (!$this->_facetedDataIsLoaded) {
            $this->loadFacetedData();
        }

        if (isset($this->_facetedData[$field])) {
            return $this->_facetedData[$field];
        }

        return array();
    }

    /**
     * Return suggestions search result data
     *
     *  @return array
     */
    public function getSuggestionsData()
    {
        return $this->_suggestionsData;
    }

    /**
     * Allow to set faceted search conditions to retrieve result by single query
     *
     * @param string $field
     * @param string | array $condition
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function setFacetCondition($field, $condition = null)
    {
        if (array_key_exists($field, $this->_facetedConditions)) {
            if (!empty($this->_facetedConditions[$field])) {
                $this->_facetedConditions[$field] = array($this->_facetedConditions[$field]);
            }
            $this->_facetedConditions[$field][] = $condition;
        } else {
            $this->_facetedConditions[$field] = $condition;
        }

        $this->_facetedDataIsLoaded = false;

        return $this;
    }

    /**
     * Add search query filter
     * Set search query
     *
     * @param   string $queryText
     *
     * @return  \Magento\Search\Model\Resource\Collection
     */
    public function addSearchFilter($queryText)
    {
        /**
         * @var \Magento\CatalogSearch\Model\Query $query
         */
        $query = $this->_catalogSearchData->getQuery();
        $this->_searchQueryText = $queryText;
        $synonymFor = $query->getSynonymFor();
        if (!empty($synonymFor)) {
            $this->_searchQueryText .= ' ' . $synonymFor;
        }

        return $this;
    }

    /**
     * Add search query filter
     * Set search query parameters
     *
     * @param   string|array $param
     * @param   string|array $value
     *
     * @return  \Magento\Search\Model\Resource\Collection
     */
    public function addSearchParam($param, $value = null)
    {
        if (is_array($param)) {
            foreach ($param as $field => $value) {
                $this->addSearchParam($field, $value);
            }
        } elseif (!empty($value)) {
            $this->_searchQueryParams[$param] = $value;
        }

        return $this;
    }

    /**
     * Get extended search parameters
     *
     * @return array
     */
    public function getExtendedSearchParams()
    {
        $result = $this->_searchQueryFilters;
        $result['query_text'] = $this->_searchQueryText;

        return $result;
    }

    /**
     * Add search query filter (fq)
     *
     * @param   array $param
     * @return  \Magento\Search\Model\Resource\Collection
     */
    public function addFqFilter($param)
    {
        if (is_array($param)) {
            foreach ($param as $field => $value) {
                $this->_searchQueryFilters[$field] = $value;
            }
        }

        return $this;
    }

    /**
     * Add advanced search query filter
     * Set search query
     *
     * @param  string $query
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function addAdvancedSearchFilter($query)
    {
        return $this->addSearchFilter($query);
    }

    /**
     * Specify category filter for product collection
     *
     * @param   \Magento\Catalog\Model\Category $category
     * @return  \Magento\Search\Model\Resource\Collection
     */
    public function addCategoryFilter(\Magento\Catalog\Model\Category $category)
    {
        $this->addFqFilter(array('category_ids' => $category->getId()));
        parent::addCategoryFilter($category);
        return $this;
    }

    /**
     * Add sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        $this->_sortBy[] = array($attribute => $dir);
        return $this;
    }

    /**
     * Prepare base parameters for search adapters
     *
     * @return array
     */
    protected function _prepareBaseParams()
    {
        $store  = \Mage::app()->getStore();
        $params = array(
            'store_id'      => $store->getId(),
            'locale_code'   => $store->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE),
            'filters'       => $this->_searchQueryFilters
        );
        $params['filters']     = $this->_searchQueryFilters;

        if (!empty($this->_searchQueryParams)) {
            $params['ignore_handler'] = true;
            $query = $this->_searchQueryParams;
        } else {
            $query = $this->_searchQueryText;
        }

        return array($query, $params);
    }

    /**
     * Search documents by query
     * Set found ids and number of found results
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    protected function _beforeLoad()
    {
        $ids = array();
        if ($this->_engine) {
            list($query, $params) = $this->_prepareBaseParams();

            if ($this->_sortBy) {
                $params['sort_by'] = $this->_sortBy;
            }
            if ($this->_pageSize !== false) {
                $page              = ($this->_curPage  > 0) ? (int) $this->_curPage  : 1;
                $rowCount          = ($this->_pageSize > 0) ? (int) $this->_pageSize : 1;
                $params['offset']  = $rowCount * ($page - 1);
                $params['limit']   = $rowCount;
            }

            $needToLoadFacetedData = (!$this->_facetedDataIsLoaded && !empty($this->_facetedConditions));
            if ($needToLoadFacetedData) {
                $params['solr_params']['facet'] = 'on';
                $params['facet'] = $this->_facetedConditions;
            }

            $result = $this->_engine->getIdsByQuery($query, $params);
            $ids    = (array) $result['ids'];

            if ($needToLoadFacetedData) {
                $this->_facetedData = $result['faceted_data'];
                $this->_facetedDataIsLoaded = true;
            }
        }

        $this->_searchedEntityIds = &$ids;
        $this->getSelect()->where('e.entity_id IN (?)', $this->_searchedEntityIds);

        /**
         * To prevent limitations to the collection, because of new data logic.
         * On load collection will be limited by _pageSize and appropriate offset,
         * but third party search engine retrieves already limited ids set
         */
        $this->_storedPageSize = $this->_pageSize;
        $this->_pageSize = false;

        return parent::_beforeLoad();
    }

    /**
     * Sort collection items by sort order of found ids
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $sortedItems = array();
        foreach ($this->_searchedEntityIds as $id) {
            if (isset($this->_items[$id])) {
                $sortedItems[$id] = $this->_items[$id];
            }
        }
        $this->_items = &$sortedItems;

        /**
         * Revert page size for proper paginator ranges
         */
        $this->_pageSize = $this->_storedPageSize;

        return $this;
    }

    /**
     * Retrieve found number of items
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            list($query, $params) = $this->_prepareBaseParams();
            $params['limit'] = 1;

            $helper = $this->_searchData;
            $searchSuggestionsEnabled = ($this->_searchQueryParams != $this->_generalDefaultQuery
                    && $helper->getSolrConfigData('server_suggestion_enabled'));
            if ($searchSuggestionsEnabled) {
                $params['solr_params']['spellcheck'] = 'true';
                $searchSuggestionsCount = (int) $helper->getSolrConfigData('server_suggestion_count');
                $params['solr_params']['spellcheck.count']  = $searchSuggestionsCount;
                $params['spellcheck_result_counts']         = (bool) $helper->getSolrConfigData(
                    'server_suggestion_count_results_enabled');
            }

            $result = $this->_engine->getIdsByQuery($query, $params);
            if ($searchSuggestionsEnabled && !empty($result['suggestions_data'])) {
                $this->_suggestionsData = $result['suggestions_data'];
            }

            $this->_totalRecords = $this->_engine->getLastNumFound();
        }

        return $this->_totalRecords;
    }

    /**
     * Collect stats per field
     *
     * @param  array $fields
     * @return array
     */
    public function getStats($fields)
    {
        list($query, $params) = $this->_prepareBaseParams();
        $params['limit'] = 0;
        $params['solr_params']['stats'] = 'true';

        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $field) {
            $params['solr_params']['stats.field'][] = $field;
        }

        return $this->_engine->getStats($query, $params);
    }

    /**
     * Set query *:* to disable query limitation
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function setGeneralDefaultQuery()
    {
        $this->_searchQueryParams = $this->_generalDefaultQuery;
        return $this;
    }

    /**
     * Set search engine
     *
     * @param object $engine
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function setEngine($engine)
    {
        $this->_engine = $engine;
        return $this;
    }

    /**
     * Stub method
     *
     * @param array $fields
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function addFieldsToFilter($fields)
    {
        return $this;
    }

    /**
     * Adding product count to categories collection
     *
     * @param   \Magento\Eav\Model\Entity\Collection\AbstractCollection $categoryCollection
     * @return  \Magento\Search\Model\Resource\Collection
     */
    public function addCountToCategories($categoryCollection)
    {
        return $this;
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param   array $visibility
     * @return  \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function setVisibility($visibility)
    {
        if (is_array($visibility)) {
            $this->addFqFilter(array('visibility' => $visibility));
        }

        return $this;
    }

    /**
     * Get prices from search results
     *
     * @param   null|float $lowerPrice
     * @param   null|float $upperPrice
     * @param   null|int   $limit
     * @param   null|int   $offset
     * @param   boolean    $getCount
     * @param   string     $sort
     * @return  array
     */
    public function getPriceData($lowerPrice = null, $upperPrice = null,
        $limit = null, $offset = null, $getCount = false, $sort = 'asc')
    {
        list($query, $params) = $this->_prepareBaseParams();
        $priceField = $this->_engine->getSearchEngineFieldName('price');
        $conditions = null;
        if (!is_null($lowerPrice) || !is_null($upperPrice)) {
            $conditions = array();
            $conditions['from'] = is_null($lowerPrice) ? 0 : $lowerPrice;
            $conditions['to'] = is_null($upperPrice) ? '' : $upperPrice;
        }
        if (!$getCount) {
            $params['fields'] = $priceField;
            $params['sort_by'] = array(array('price' => $sort));
            if (!is_null($limit)) {
                $params['limit'] = $limit;
            }
            if (!is_null($offset)) {
                $params['offset'] = $offset;
            }
            if (!is_null($conditions)) {
                $params['filters'][$priceField] = $conditions;
            }
        } else {
            $params['solr_params']['facet'] = 'on';
            if (is_null($conditions)) {
                $conditions = array('from' => 0, 'to' => '');
            }
            $params['facet'][$priceField] = array($conditions);
        }

        $data = $this->_engine->getResultForRequest($query, $params);
        if ($getCount) {
            return array_shift($data['faceted_data'][$priceField]);
        }
        $result = array();
        foreach ($data['ids'] as $value) {
            $result[] = (float)$value[$priceField];
        }

        return ($sort == 'asc') ? $result : array_reverse($result);
    }
}
