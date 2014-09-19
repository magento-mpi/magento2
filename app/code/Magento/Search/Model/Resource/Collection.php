<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Resource;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

/**
 * Enterprise search collection resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Catalog\Model\Resource\Product\Collection
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
     * @var int[]
     */
    protected $foundEntityIds;

    /**
     * Store found suggestions
     *
     * @var array
     */
    protected $_searchedSuggestions = array();

    /**
     * Store engine instance
     *
     * @var \Magento\Search\Model\Resource\Solr\Engine
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
    protected $_catalogSearchData;

    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param mixed $connection
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\Resource\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Search\Helper\Data $searchData,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        $connection = null
    ) {
        $this->_searchData = $searchData;
        $this->_catalogSearchData = $catalogSearchData;
        $this->_localeResolver = $localeResolver;
        $this->_engine = $engineProvider->get();
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $connection
        );
    }

    /**
     * Load faceted data if not loaded
     *
     * @return $this
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
     * @return array
     */
    public function getSuggestionsData()
    {
        return $this->_suggestionsData;
    }

    /**
     * Allow to set faceted search conditions to retrieve result by single query
     *
     * @param string $field
     * @param string|array|null $condition
     * @return $this
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
     * @param string $queryText
     * @return $this
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
     * @param string|array $param
     * @param string|array|null $value
     * @return $this
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
     * @param array $param
     * @return $this
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
     * @param string $query
     * @return $this
     */
    public function addAdvancedSearchFilter($query)
    {
        return $this->addSearchFilter($query);
    }

    /**
     * Specify category filter for product collection
     *
     * @param Category $category
     * @return $this
     */
    public function addCategoryFilter(Category $category)
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
     * @return $this
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
        $store = $this->_storeManager->getStore();
        $localeCode = $this->_scopeConfig->getValue(
            $this->_localeResolver->getDefaultLocalePath(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $params = array(
            'store_id' => $store->getId(),
            'locale_code' => $localeCode,
            'filters' => $this->_searchQueryFilters
        );
        $params['filters'] = $this->_searchQueryFilters;

        if (!empty($this->_searchQueryParams)) {
            $params['ignore_handler'] = true;
            $query = $this->_searchQueryParams;
        } else {
            $query = $this->_searchQueryText;
        }

        return array($query, $params);
    }

    /**
     * Find matched products and add them to select
     *
     * @return void
     */
    protected function addFoundProductFilter()
    {
        if (is_null($this->foundEntityIds)) {
            list($query, $params) = $this->_prepareBaseParams();

            $helper = $this->_searchData;
            $searchSuggestionsEnabled = $this->_searchQueryParams != $this->_generalDefaultQuery
                && $helper->getSolrConfigData('server_suggestion_enabled');
            if ($searchSuggestionsEnabled) {
                $params['solr_params']['spellcheck'] = 'true';
                $searchSuggestionsCount = (int)$helper->getSolrConfigData('server_suggestion_count');
                $params['solr_params']['spellcheck.count'] = $searchSuggestionsCount;
                $params['spellcheck_result_counts'] = (bool)$helper->getSolrConfigData(
                    'server_suggestion_count_results_enabled'
                );
            }

            if ($this->_sortBy) {
                $params['sort_by'] = $this->_sortBy;
            }

            $needToLoadFacetedData = !$this->_facetedDataIsLoaded && !empty($this->_facetedConditions);
            if ($needToLoadFacetedData) {
                $params['solr_params']['facet'] = 'on';
                $params['facet'] = $this->_facetedConditions;
            }

            $result = $this->_engine->getIdsByQuery($query, $params);

            if ($searchSuggestionsEnabled && !empty($result['suggestions_data'])) {
                $this->_suggestionsData = $result['suggestions_data'];
            }

            if ($needToLoadFacetedData) {
                $this->_facetedData = $result['faceted_data'];
                $this->_facetedDataIsLoaded = true;
            }

            $this->foundEntityIds = (array)$result['ids'];
            $this->getSelect()->where('e.entity_id IN (?)', $this->foundEntityIds)
                ->order(new \Zend_Db_Expr($this->_conn->quoteInto('FIELD(e.entity_id, ?)', $this->foundEntityIds)));
        }
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        $this->addFoundProductFilter();

        return parent::getSize();
    }

    /**
     * Load collection data into object items
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        $this->addFoundProductFilter();

        return parent::load($printQuery, $logQuery);
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
     * @return $this
     */
    public function setGeneralDefaultQuery()
    {
        $this->_searchQueryParams = $this->_generalDefaultQuery;
        return $this;
    }

    /**
     * Stub method
     *
     * @param array $fields
     * @return $this
     */
    public function addFieldsToFilter($fields)
    {
        return $this;
    }

    /**
     * Adding product count to categories collection
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $categoryCollection
     * @return $this
     */
    public function addCountToCategories($categoryCollection)
    {
        $this->addFoundProductFilter();

        return parent::addCountToCategories($categoryCollection);
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param array $visibility
     * @return $this
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
     * @param null|float $lowerPrice
     * @param null|float $upperPrice
     * @param null|int $limit
     * @param null|int $offset
     * @param bool $getCount
     * @param string $sort
     * @return array
     */
    public function getPriceData(
        $lowerPrice = null,
        $upperPrice = null,
        $limit = null,
        $offset = null,
        $getCount = false,
        $sort = 'asc'
    ) {
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
            $result[] = (double)$value[$priceField];
        }

        return $sort == 'asc' ? $result : array_reverse($result);
    }
}
