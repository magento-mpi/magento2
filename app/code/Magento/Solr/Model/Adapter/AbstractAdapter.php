<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Adapter;

/**
 * Search engine abstract adapter
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractAdapter
{
    /**
     * @var \Magento\Solr\Model\Resource\Index
     */
    protected $_resourceIndex;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    protected $_attributeCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Cache
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * Field to use to determine and enforce document uniqueness
     */
    const UNIQUE_KEY = 'unique';

    /**
     * Store Solr Client instance
     *
     * @var object
     */
    protected $_client = null;

    /**
     * Object name used to create solr document object
     *
     * @var string
     */
    protected $_clientDocObjectName = 'Apache_Solr_Document';

    /**
     * Store last search query number of found results
     *
     * @var int
     */
    protected $_lastNumFound = 0;

    /**
     * Search query filters
     *
     * @var array
     */
    protected $_filters = [];

    /**
     * Store common Solr metadata fields
     * All fields, that come up from search engine will be filtered by these keys
     *
     * @var string[]
     */
    protected $_usedFields = ['sku', 'visibility'];

    /**
     * Defines text type fields
     *
     * @var string[]
     */
    protected $_textFieldTypes = ['text', 'varchar'];

    /**
     * Search query params with their default values
     *
     * @var array
     */
    protected $_defaultQueryParams = [
        'offset' => 0,
        'limit' => 40000,
        'sort_by' => [['score' => 'desc']],
        'store_id' => null,
        'locale_code' => null,
        'fields' => [],
        'solr_params' => [],
        'ignore_handler' => false,
        'filters' => [],
    ];

    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator = ' ';

    /**
     * Searchable attribute params
     *
     * @var array|null
     */
    protected $_indexableAttributeParams = null;

    /**
     * Define if automatic commit on changes for adapter is allowed
     *
     * @var bool
     */
    protected $_holdCommit = false;

    /**
     * Define if search engine index needs optimization
     *
     * @var bool
     */
    protected $_indexNeedsOptimization = false;

    // Deprecated properties

    /**
     * Fields which must be are not included in fulltext field
     *
     * @var string[]
     * @deprecated after 1.11.2.0
     */
    protected $_notInFulltextField = [
        self::UNIQUE_KEY,
        'id',
        'store_id',
        'category_ids',
        'visibility',
    ];

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Solr\Model\Resource\Index $resourceIndex
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\Collection $attributeCollection
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Solr\Model\Resource\Index $resourceIndex,
        \Magento\Catalog\Model\Resource\Product\Attribute\Collection $attributeCollection,
        \Magento\Framework\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->_customerSession = $customerSession;
        $this->_resourceIndex = $resourceIndex;
        $this->_attributeCollection = $attributeCollection;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_cache = $cache;
    }

    /**
     * Retrieve attribute field name
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute|string $attribute
     * @param string $target
     * @return string|bool
     * @abstract
     */
    abstract public function getSearchEngineFieldName($attribute, $target = 'default');

    /**
     * Before commit action
     *
     * @return $this
     */
    protected function _beforeCommit()
    {
        return $this;
    }

    /**
     * After commit action
     *
     * @return $this
     */
    protected function _afterCommit()
    {
        /**
         * Cleaning MAXPRICE cache
         */
        $cacheTag = \Magento\Solr\Model\Layer\Category\Filter\Price::CACHE_TAG;
        $this->_cache->clean([$cacheTag]);

        $this->_indexNeedsOptimization = true;

        return $this;
    }

    /**
     * Before optimize action.
     * _beforeCommit method is called because optimize includes commit in itself
     *
     * @return $this
     */
    protected function _beforeOptimize()
    {
        $this->_beforeCommit();

        return $this;
    }

    /**
     * After commit action
     * _afterCommit method is called because optimize includes commit in itself
     *
     * @return $this
     */
    protected function _afterOptimize()
    {
        $this->_afterCommit();

        $this->_indexNeedsOptimization = false;

        return $this;
    }

    /**
     * Store searchable attributes to prevent additional collection load
     *
     * @param array $attributes
     * @return $this
     */
    public function storeSearchableAttributes(array $attributes)
    {
        $result = [];
        foreach ($attributes as $attribute) {
            $result[$attribute->getAttributeCode()] = $attribute;
        }

        $this->_indexableAttributeParams = $result;
        return $this;
    }

    /**
     * Prepare name for system text fields.
     *
     * @param string $field
     * @param string $suffix
     * @param int|null $storeId
     * @return string
     */
    public function getAdvancedTextFieldName($field, $suffix = '', $storeId = null)
    {
        return $field;
    }

    /**
     * Prepare price field name for search engine
     *
     * @param null|int $customerGroupId
     * @param null|int $websiteId
     * @return false|string
     */
    public function getPriceFieldName($customerGroupId = null, $websiteId = null)
    {
        if ($customerGroupId === null) {
            $customerGroupId = $this->_customerSession->getCustomerGroupId();
        }
        if ($websiteId === null) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }

        if ($customerGroupId === null || !$websiteId) {
            return false;
        }

        return 'price_' . $customerGroupId . '_' . $websiteId;
    }

    /**
     * Prepare category index data for product
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    protected function _prepareProductCategoryIndexData($productId, $storeId)
    {
        $result = [];

        $categoryProductData = $this->_resourceIndex->getCategoryProductIndexData($storeId, $productId);

        if (isset($categoryProductData[$productId])) {
            $categoryProductData = $categoryProductData[$productId];

            $categoryIds = array_keys($categoryProductData);
            if (!empty($categoryIds)) {
                $result = ['category_ids' => $categoryIds];

                foreach ($categoryProductData as $categoryId => $position) {
                    $result['position_category_' . $categoryId] = $position;
                }
            }
        }

        return $result;
    }

    /**
     * Prepare price index for product
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    protected function _preparePriceIndexData($productId, $storeId)
    {
        $result = [];

        $productPriceIndexData = $this->_resourceIndex->getPriceIndexData($productId, $storeId);

        if (isset($productPriceIndexData[$productId])) {
            $productPriceIndexData = $productPriceIndexData[$productId];

            $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
            foreach ($productPriceIndexData as $customerGroupId => $price) {
                $fieldName = $this->getPriceFieldName($customerGroupId, $websiteId);
                $result[$fieldName] = sprintf('%F', $price);
            }
        }

        return $result;
    }

    /**
     * Is data available in index
     *
     * @param array $productIndexData
     * @param int $productId
     * @return bool
     */
    protected function isAvailableInIndex($productIndexData, $productId)
    {
        if (!is_array($productIndexData) || empty($productIndexData)) {
            return false;
        }

        if (!isset($productIndexData['visibility'][$productId])) {
            return false;
        }

        return true;
    }

    /**
     * Prepare index data for using in search engine metadata.
     * Prepare fields for advanced search, navigation, sorting and fulltext fields for each search weight for
     * quick search and spell.
     *
     * @param array $productIndexData
     * @param int $productId
     * @param int $storeId
     * @return false|array
     */
    protected function _prepareIndexProductData($productIndexData, $productId, $storeId)
    {
        if (!$this->isAvailableInIndex($productIndexData, $productId)) {
            return false;
        }

        $fulltextData = [];
        foreach ($productIndexData as $attributeCode => $value) {
            if ($attributeCode == 'visibility') {
                $productIndexData[$attributeCode] = $value[$productId];
                continue;
            }

            // Prepare processing attribute info
            if (isset($this->_indexableAttributeParams[$attributeCode])) {
                /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                $attribute = $this->_indexableAttributeParams[$attributeCode];
            } else {
                $attribute = null;
            }

            // Prepare values for required fields
            if (!in_array($attributeCode, $this->_usedFields)) {
                unset($productIndexData[$attributeCode]);
            }

            if (!$attribute || $attributeCode == 'price' || empty($value)) {
                continue;
            }

            $attribute->setStoreId($storeId);
            $preparedValue = '';
            // Preparing data for solr fields
            if ($attribute->getIsSearchable() ||
                $attribute->getIsVisibleInAdvancedSearch() ||
                $attribute->getIsFilterable() ||
                $attribute->getIsFilterableInSearch() ||
                $attribute->getUsedForSortBy()
            ) {
                $backendType = $attribute->getBackendType();
                $frontendInput = $attribute->getFrontendInput();

                if ($attribute->usesSource()) {
                    if ($frontendInput == 'multiselect') {
                        $preparedValue = [];
                        foreach ($value as $val) {
                            $preparedValue = array_merge($preparedValue, explode(',', $val));
                        }
                        $preparedNavValue = $preparedValue;
                    } else {
                        // safe condition
                        if (!is_array($value)) {
                            $preparedValue = [$value];
                        } else {
                            $preparedValue = array_unique($value);
                        }

                        $preparedNavValue = $preparedValue;
                        // Ensure that self product value will be saved after array_unique function for sorting purpose
                        if (isset($value[$productId])) {
                            if (!isset($preparedNavValue[$productId])) {
                                $selfValueKey = array_search($value[$productId], $preparedNavValue);
                                unset($preparedNavValue[$selfValueKey]);
                                $preparedNavValue[$productId] = $value[$productId];
                            }
                        }
                    }

                    foreach ($preparedValue as $id => $val) {
                        $preparedValue[$id] = $attribute->getSource()->getIndexOptionText($val);
                    }
                } else {
                    $preparedValue = $value;
                    if ($backendType == 'datetime') {
                        if (is_array($value)) {
                            $preparedValue = [];
                            foreach ($value as $id => &$val) {
                                $val = $this->_getSolrDate($storeId, $val);
                                if (!empty($val)) {
                                    $preparedValue[$id] = $val;
                                }
                            }
                            unset($val);
                            //clear link to value
                            $preparedValue = array_unique($preparedValue);
                        } else {
                            $preparedValue[$productId] = $this->_getSolrDate($storeId, $value);
                        }
                    }
                }
            }

            // Preparing data for sorting field
            if ($attribute->getUsedForSortBy()) {
                $sortValue = null;
                if (is_array($preparedValue)) {
                    if (isset($preparedValue[$productId])) {
                        $sortValue = $preparedValue[$productId];
                    } else {
                        $sortValue = null;
                    }
                }

                if (!empty($sortValue)) {
                    $fieldName = $this->getSearchEngineFieldName($attribute, 'sort');

                    if ($fieldName) {
                        $productIndexData[$fieldName] = $sortValue;
                    }
                }
            }

            // Adding data for advanced search field (without additional prefix)
            if ($attribute->getIsVisibleInAdvancedSearch() ||
                $attribute->getIsFilterable() ||
                $attribute->getIsFilterableInSearch()
            ) {
                if ($attribute->usesSource()) {
                    $fieldName = $this->getSearchEngineFieldName($attribute, 'nav');
                    if ($fieldName && !empty($preparedNavValue)) {
                        $productIndexData[$fieldName] = $preparedNavValue;
                    }
                } else {
                    $fieldName = $this->getSearchEngineFieldName($attribute);
                    if ($fieldName && !empty($preparedValue)) {
                        $productIndexData[$fieldName] = in_array(
                            $backendType,
                            $this->_textFieldTypes
                        ) ? implode(
                            ' ',
                            (array)$preparedValue
                        ) : $preparedValue;
                    }
                }
            }

            // Adding data for fulltext search field
            if ($attribute->getIsSearchable() && !empty($preparedValue)) {
                $searchWeight = $attribute->getSearchWeight();
                if ($searchWeight) {
                    $fulltextData[$searchWeight][] = is_array(
                        $preparedValue
                    ) ? implode(
                        ' ',
                        $preparedValue
                    ) : $preparedValue;
                }
            }

            unset($preparedNavValue, $preparedValue, $fieldName, $attribute);
        }

        // Preparing fulltext search fields
        $fulltextSpell = [];
        foreach ($fulltextData as $searchWeight => $data) {
            $fieldName = $this->getAdvancedTextFieldName('fulltext', $searchWeight, $storeId);
            $productIndexData[$fieldName] = $this->_implodeIndexData($data);
            $fulltextSpell += $data;
        }
        unset($fulltextData);

        // Preparing field with spell info
        $fulltextSpell = array_unique($fulltextSpell);
        $fieldName = $this->getAdvancedTextFieldName('spell', '', $storeId);
        $productIndexData[$fieldName] = $this->_implodeIndexData($fulltextSpell);
        unset($fulltextSpell);

        // Getting index data for price
        if (isset($this->_indexableAttributeParams['price'])) {
            $priceEntityIndexData = $this->_preparePriceIndexData($productId, $storeId);
            $productIndexData = array_merge($productIndexData, $priceEntityIndexData);
        }

        // Product category index data definition
        $productCategoryIndexData = $this->_prepareProductCategoryIndexData($productId, $storeId);
        $productIndexData = array_merge($productIndexData, $productCategoryIndexData);

        // Define system data for engine internal usage
        $productIndexData['id'] = $productId;
        $productIndexData['store_id'] = $storeId;
        $productIndexData[self::UNIQUE_KEY] = $productId . '|' . $storeId;

        return $productIndexData;
    }

    /**
     * Create Solr Input Documents by specified data
     *
     * @param array $docData
     * @param int $storeId
     * @return array
     */
    public function prepareDocsPerStore($docData, $storeId)
    {
        if (!is_array($docData) || empty($docData)) {
            return [];
        }

        $docs = [];
        foreach ($docData as $productId => $productIndexData) {
            $doc = new $this->_clientDocObjectName();

            $productIndexData = $this->_prepareIndexProductData($productIndexData, $productId, $storeId);
            if (!$productIndexData) {
                continue;
            }

            foreach ($productIndexData as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if (!is_array($val)) {
                            $doc->addField($name, $val);
                        }
                    }
                } else {
                    $doc->addField($name, $value);
                }
            }
            $docs[] = $doc;
        }

        return $docs;
    }

    /**
     * Add prepared Solr Input documents to Solr index
     *
     * @param array $docs
     * @return $this
     */
    public function addDocs($docs)
    {
        if (empty($docs)) {
            return $this;
        }
        if (!is_array($docs)) {
            $docs = [$docs];
        }

        $_docs = [];
        foreach ($docs as $doc) {
            if ($doc instanceof $this->_clientDocObjectName) {
                $_docs[] = $doc;
            }
        }

        if (empty($_docs)) {
            return $this;
        }

        try {
            $this->_client->addDocuments($_docs);
        } catch (\Exception $e) {
            $this->rollback();
            $this->_logger->logException($e);
        }

        $this->commit();

        return $this;
    }

    /**
     * Remove documents from Solr index
     *
     * @param int|string|array $docIDs
     * @param string|array|null $queries if "all" specified and $docIDs are empty, then all documents will be removed
     * @return $this
     */
    public function deleteDocs($docIDs = [], $queries = null)
    {
        $_deleteBySuffix = 'Ids';
        $params = [];
        if (!empty($docIDs)) {
            if (!is_array($docIDs)) {
                $docIDs = [$docIDs];
            }
            $params = $docIDs;
        } elseif (!empty($queries)) {
            if ($queries == 'all') {
                $queries = ['*:*'];
            }
            if (!is_array($queries)) {
                $queries = [$queries];
            }
            $_deleteBySuffix = 'Queries';
            $params = $queries;
        }

        if ($params) {
            $deleteMethod = sprintf('deleteBy%s', $_deleteBySuffix);

            try {
                $this->_client->{$deleteMethod}($params);
            } catch (\Exception $e) {
                $this->rollback();
                $this->_logger->logException($e);
            }

            $this->commit();
        }

        return $this;
    }

    /**
     * Retrieve found document ids from Solr index sorted by relevance
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getIdsByQuery($query, $params = [])
    {
        $params['fields'] = ['id'];

        $result = $this->_search($query, $params);

        if (!isset($result['ids'])) {
            $result['ids'] = [];
        }

        if (!empty($result['ids'])) {
            foreach ($result['ids'] as &$id) {
                $id = $id['id'];
            }
        }

        return $result;
    }

    /**
     * Collect statistics about specified fields
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getStats($query, $params = [])
    {
        return $this->_search($query, $params);
    }

    /**
     * Search documents in Solr index sorted by relevance
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function search($query, $params = [])
    {
        return $this->_search($query, $params);
    }

    /**
     * Finalizes all add/deletes made to the index
     *
     * @return object|false
     */
    public function commit()
    {
        if ($this->_holdCommit) {
            return false;
        }

        $this->_beforeCommit();
        $result = $this->_client->commit();
        $this->_afterCommit();

        return $result;
    }

    /**
     * Perform optimize operation
     * Same as commit operation, but also defragment the index for faster search performance
     *
     * @return object|false
     */
    public function optimize()
    {
        if ($this->_holdCommit) {
            return false;
        }

        $this->_beforeOptimize();
        $result = $this->_client->optimize();
        $this->_afterOptimize();

        return $result;
    }

    /**
     * Rollbacks all add/deletes made to the index since the last commit
     *
     * @return object
     */
    public function rollback()
    {
        return $this->_client->rollback();
    }

    /**
     * Getter for field to use to determine and enforce document uniqueness
     *
     * @return string
     */
    public function getUniqueKey()
    {
        return self::UNIQUE_KEY;
    }

    /**
     * Retrieve last query number of found results
     *
     * @return int
     */
    public function getLastNumFound()
    {
        return $this->_lastNumFound;
    }

    /**
     * Connect to Search Engine Client by specified options.
     * Should initialize _client
     *
     * @param array $options
     * @return SolrClient|\Magento\Solr\Model\Client\Solr
     */
    abstract protected function _connect($options = []);

    /**
     * Simple Search interface
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    abstract protected function _search($query, $params = []);

    /**
     * Checks if Solr server is still up
     *
     * @return bool
     */
    abstract public function ping();

    /**
     * Retrieve language code by specified locale code if this locale is supported
     *
     * @param string $localeCode
     * @return false|string
     */
    abstract protected function _getLanguageCodeByLocaleCode($localeCode);

    /**
     * Convert Solr Query Response found documents to an array
     *
     * @param object $response
     * @return array
     */
    protected function _prepareQueryResponse($response)
    {
        $realResponse = $response->response;
        $_docs = $realResponse->docs;
        if (!$_docs) {
            return [];
        }
        $this->_lastNumFound = (int)$realResponse->numFound;
        $result = [];
        foreach ($_docs as $doc) {
            $result[] = $this->_objectToArray($doc);
        }

        return $result;
    }

    /**
     * Convert Solr Query Response found suggestions to string
     *
     * @param object $response
     * @return array
     */
    protected function _prepareSuggestionsQueryResponse($response)
    {
        $suggestions = [];

        if (array_key_exists('spellcheck', $response) && array_key_exists('suggestions', $response->spellcheck)) {
            $arrayResponse = $this->_objectToArray($response->spellcheck->suggestions);
            if (is_array($arrayResponse)) {
                foreach ($arrayResponse as $item) {
                    if (isset($item['suggestion']) && is_array($item['suggestion']) && !empty($item['suggestion'])) {
                        $suggestions = array_merge($suggestions, $item['suggestion']);
                    }
                }
            }

            // It is assumed that the frequency corresponds to the number of results
            if (count($suggestions)) {
                usort($suggestions, [get_class($this), 'sortSuggestions']);
            }
        }

        return $suggestions;
    }

    /**
     * Convert Solr Query Response found facets to array
     *
     * @param object $response
     * @return array
     */
    protected function _prepareFacetsQueryResponse($response)
    {
        return $this->_facetObjectToArray($response->facet_counts);
    }

    /**
     * Convert Solr Query Response collected statistics to array
     *
     * @param object $response
     * @return array
     */
    protected function _prepateStatsQueryResponce($response)
    {
        return $this->_objectToArray($response->stats->stats_fields);
    }

    /**
     * Callback function for sort search suggestions
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function sortSuggestions($a, $b)
    {
        return $a['freq'] > $b['freq'] ? -1 : ($a['freq'] < $b['freq'] ? 1 : 0);
    }

    /**
     * Escape query text
     *
     * @param string $text
     * @return string
     */
    protected function _prepareQueryText($text)
    {
        $words = explode(' ', $text);
        if (count($words) > 1) {
            foreach ($words as $key => &$val) {
                if (!empty($val)) {
                    $val = $this->_escape($val);
                } else {
                    unset($words[$key]);
                }
            }
            $text = '(' . implode(' ', $words) . ')';
        } else {
            $text = $this->_escape($text);
        }

        return $text;
    }

    /**
     * Escape filter query text
     *
     * @param string $text
     * @return string
     */
    protected function _prepareFilterQueryText($text)
    {
        $words = explode(' ', trim($text));
        if (count($words) > 1) {
            $text = $this->_phrase($text);
        } else {
            $text = $this->_escape($text);
        }

        return $text;
    }

    /**
     * Implode index array to string by separator
     * Support 2 level array gluing
     *
     * @param array $indexData
     * @param string $separator
     * @return string
     */
    protected function _implodeIndexData($indexData, $separator = ' ')
    {
        if (!$indexData) {
            return '';
        }
        if (is_string($indexData)) {
            return $indexData;
        }

        $_index = [];
        if (!is_array($indexData)) {
            $indexData = [$indexData];
        }

        foreach ($indexData as $value) {
            if (!is_array($value)) {
                $_index[] = $value;
            } else {
                $_index = array_merge($_index, $value);
            }
        }
        $_index = array_unique($_index);

        return implode($separator, $_index);
    }

    /**
     * Escape a value for special query characters such as ':', '(', ')', '*', '?', etc.
     *
     * @param string $value
     * @return string
     * @link http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
     */
    public function _escape($value)
    {
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';

        return preg_replace($pattern, $replace, $value);
    }

    /**
     * Escape a value meant to be contained in a phrase for special query characters
     *
     * @param string $value
     * @return string
     */
    public function _escapePhrase($value)
    {
        $pattern = '/("|\\\)/';
        $replace = '\\\$1';

        return preg_replace($pattern, $replace, $value);
    }

    /**
     * Convenience function for creating phrase syntax from a value
     *
     * @param string $value
     * @return string
     */
    public function _phrase($value)
    {
        return '"' . $this->_escapePhrase($value) . '"';
    }

    /**
     * Prepare solr field condition
     *
     * @param string $field
     * @param string $value
     * @return string
     */
    protected function _prepareFieldCondition($field, $value)
    {
        $fieldCondition = $field . ':' . $value;

        return $fieldCondition;
    }

    /**
     * Convert an object to an array
     *
     * @param object $object The object to convert
     * @return array
     */
    protected function _objectToArray($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = get_object_vars($object);
        }

        return array_map([$this, '_objectToArray'], $object);
    }

    /**
     * Convert facet results object to an array
     *
     * @param object|array $object
     * @return array
     */
    protected function _facetObjectToArray($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }

        if (is_object($object)) {
            $object = get_object_vars($object);
        }

        $res = [];
        foreach ($object['facet_fields'] as $attr => $val) {
            foreach ($val as $key => $value) {
                $res[$attr][$key] = $value;
            }
        }

        foreach ($object['facet_queries'] as $attr => $val) {
            $attrArray = explode(':', $attr);
            $res[$attrArray[0]][$attrArray[1]] = $val;
        }

        return $res;
    }

    /**
     * Hold commit of changes for adapter
     *
     * @return $this
     */
    public function holdCommit()
    {
        $this->_holdCommit = true;
        return $this;
    }

    /**
     * Allow changes commit for adapter
     *
     * @return $this
     */
    public function allowCommit()
    {
        $this->_holdCommit = false;
        return $this;
    }

    /**
     * Define if third party search engine index needs optimization
     *
     * @param bool $state
     * @return $this
     */
    public function setIndexNeedsOptimization($state = true)
    {
        $this->_indexNeedsOptimization = (bool)$state;
        return $this;
    }

    /**
     * Check if third party search engine index needs optimization
     *
     * @return bool
     */
    public function getIndexNeedsOptimization()
    {
        return $this->_indexNeedsOptimization;
    }

    // Deprecated methods


    /**
     * Create Solr Input Documents by specified data
     *
     * @param  array $docData
     * @param  string|null $localeCode
     * @return array
     * @deprecated after 1.11.2.0
     */
    public function prepareDocs($docData, $localeCode)
    {
        return [];
    }

    /**
     * Retrieve attributes selected parameters
     *
     * @return array
     * @deprecated after 1.11.2.0
     */
    protected function _getIndexableAttributeParams()
    {
        if ($this->_indexableAttributeParams === null) {
            $attributeCollection = $this->_attributeCollection->addToIndexFilter()->getItems();

            $this->_indexableAttributeParams = [];
            foreach ($attributeCollection as $item) {
                $this->_indexableAttributeParams[$item->getAttributeCode()] = [
                    'backendType' => $item->getBackendType(),
                    'frontendInput' => $item->getFrontendInput(),
                    'searchWeight' => $item->getSearchWeight(),
                    'isSearchable' => (bool)$item->getIsSearchable(),
                ];
            }
        }

        return $this->_indexableAttributeParams;
    }

    /**
     * Ability extend document index data.
     *
     * @param array $data
     * @param array $attributesParams
     * @param string|null $localeCode
     * @return array
     * @deprecated after 1.11.2.0
     */
    protected function _prepareIndexData($data, $attributesParams = [], $localeCode = null)
    {
        return $data;
    }
}
