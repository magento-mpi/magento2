<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search engine abstract adapter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Search_Model_Adapter_Abstract
{
    /**
     * Field to use to determine and enforce document uniqueness
     *
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
    protected $_filters = array();

    /**
     * Store common Solr metadata fields
     * All fields, that come up from search engine will be filtered by these keys
     *
     * @var array
     */
    protected $_usedFields = array(
        self::UNIQUE_KEY,
        'id',
        'sku',
        'price',
        'store_id',
        'categories',
        'show_in_categories',
        'visibility',
        'in_stock',
        'score'
    );

    /**
     * Fields which must be are not included in fulltext field
     *
     * @var array
     */
    protected $_notInFulltextField = array(
        self::UNIQUE_KEY,
        'id',
        'store_id',
        'in_stock',
        'categories',
        'show_in_categories',
        'visibility'
    );

    /**
     * Defines text type fields
     *
     * @var array
     */
    protected $_textFieldTypes = array('text', 'varchar');

    /**
     * Search query params with their default values
     *
     * @var array
     */
    protected $_defaultQueryParams = array(
        'offset'         => 0,
        'limit'          => 100,
        'sort_by'        => array(array('score' => 'desc')),
        'store_id'       => null,
        'locale_code'    => null,
        'fields'         => array(),
        'solr_params'    => array(),
        'ignore_handler' => false,
        'filters'        => array()
    );

    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator = ' ';

    /**
     * Searchable attribute params
     *
     * @var array
     */
    protected $_indexableAttributeParams;

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

    /**
     * Before commit action
     *
     * @return Enterprise_Search_Model_Adapter_Abstract
     */
    protected function _beforeCommit()
    {
        return $this;
    }

    /**
     * After commit action
     *
     * @return Enterprise_Search_Model_Adapter_Abstract
     */
    protected function _afterCommit()
    {
        /**
         * Cleaning MAXPRICE cache
         */
        $cacheTag = Mage::getSingleton('Enterprise_Search_Model_Catalog_Layer_Filter_Price')->getCacheTag();
        Mage::app()->cleanCache(array($cacheTag));

        $this->_indexNeedsOptimization = true;

        return $this;
    }

    /**
     * Before optimize action.
     * _beforeCommit method is called because optimize includes commit in itself
     *
     * @return Enterprise_Search_Model_Adapter_Abstract
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
     * @return Enterprise_Search_Model_Adapter_Abstract
     */
    protected function _afterOptimize()
    {
        $this->_afterCommit();

        $this->_indexNeedsOptimization = false;

        return $this;
    }

    /**
     * Retrieve attributes selected parameters
     *
     * @return array
     */
    protected function _getIndexableAttributeParams()
    {
        if (empty($this->_searchableAttributeParams)) {
            $entityTypeId = Mage::getSingleton('Mage_Eav_Model_Config')
                ->getEntityType('catalog_product')
                ->getEntityTypeId();
            $items = Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Product_Attribute_Collection')
                ->setEntityTypeFilter($entityTypeId)
                ->addToIndexFilter()
                ->getItems();

            $this->_indexableAttributeParams = array();
            foreach ($items as $item) {
                $this->_indexableAttributeParams[$item->getAttributeCode()] = array(
                    'backendType'   => $item->getBackendType(),
                    'frontendInput' => $item->getFrontendInput(),
                    'searchWeight'  => $item->getSearchWeight(),
                    'isSearchable'  => $item->getIsSearchable()
                );
            }
        }

        return $this->_indexableAttributeParams;
    }

    /**
     * Create Solr Input Documents by specified data
     *
     * @param array $docData
     * @param string|null $localeCode
     * @return array
     */
    public function prepareDocs($docData, $localeCode = null)
    {
        if (!is_array($docData) || empty($docData)) {
            return array();
        }

        $docs = array();
        $attributeParams = $this->_getIndexableAttributeParams();
        $this->_separator = Mage::getResourceSingleton('Mage_CatalogSearch_Model_Resource_Fulltext')->getSeparator();
        $fieldPrefix = Mage::getResourceSingleton('Enterprise_Search_Model_Resource_Engine')->getFieldsPrefix();
        $fieldPrefixLength = strlen($fieldPrefix);

        foreach ($docData as $entityId => $index) {
            $doc = new $this->_clientDocObjectName;

            $index[self::UNIQUE_KEY] = $entityId . '|' . $index['store_id'];
            $index['id'] = $entityId;

            $fulltext = $index;
            foreach ($this->_notInFulltextField as $field) {
                if (isset($fulltext[$field])) {
                    unset($fulltext[$field]);
                }
            }

            /**
             * Merge attributes to fulltext fields according to their search weights
             */
            $attributesWeights = array();
            $attributesSpell = array();
            $needToReplaceSeparator = ($this->_separator != ' ');
            $currentCurrency = Mage::app()->getStore($index['store_id'])->getDefaultCurrency();
            foreach ($index as $code => $value) {
                $weight = 0;
                $isSearchable = 0;

                if (!empty($attributeParams[$code])) {
                    $weight = $attributeParams[$code]['searchWeight'];
                    $frontendInput = $attributeParams[$code]['frontendInput'];
                    $isSearchable = $attributeParams[$code]['isSearchable'];
                } elseif ((substr($code, 0, 5 + $fieldPrefixLength) == $fieldPrefix . 'price'
                        && !empty($attributeParams['price']))) {
                    $weight = $attributeParams['price']['searchWeight'];
                    $frontendInput = 'price';
                    $isSearchable = $attributeParams['price']['isSearchable'];
                }

                if ($weight && $isSearchable) {
                    if ($needToReplaceSeparator && $frontendInput == 'multiselect') {
                        $value = str_replace($this->_separator, ' ', $value);
                    } elseif ($code == 'price' || $frontendInput == 'price') {
                        if (!is_array($value)) {
                            $value = array($value);
                        }

                        foreach ($value as $key => $price) {
                            if ($price == round($price, 0)) {
                                $value[] = $currentCurrency->formatPrecision($price, 0, array(), false);
                            } elseif ($price == round($price, 1)) {
                                $value[] = $currentCurrency->formatPrecision($price, 1, array(), false);
                            }
                            $value[$key] = $currentCurrency->formatPrecision($price, 2, array(), false);
                        }
                    }

                    $attributesWeights['fulltext' . $weight][] = $value;
                    $attributesSpell[] = $value;
                }
            }
            $index['fulltext_spell'] = $this->_implodeIndexData($attributesSpell);

            foreach ($attributesWeights as $key => $value) {
                $index[$key] = $this->_implodeIndexData($value);
            }

            $index = $this->_prepareIndexData($index, $attributeParams, $localeCode);
            if (!$index) {
                continue;
            }

            foreach ($index as $name => $value) {
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
     * Ability extend document index data.
     *
     * @param array $data
     * @param array $attributesParams
     * @param string|null $localCode
     *
     * @return array
     */
    protected function _prepareIndexData($data, $attributesParams, $localeCode = null)
    {
        return $data;
    }

    /**
     * Add prepared Solr Input documents to Solr index
     *
     * @param array $docs
     * @return Enterprise_Search_Model_Adapter_Solr
     */
    public function addDocs($docs)
    {
        if (empty($docs)) {
            return $this;
        }
        if (!is_array($docs)) {
            $docs = array($docs);
        }

        $_docs = array();
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
        } catch (Exception $e) {
            $this->rollback();
            Mage::logException($e);
        }

        $this->commit();

        return $this;
    }

    /**
     * Remove documents from Solr index
     *
     * @param  int|string|array $docIDs
     * @param  string|array|null $queries if "all" specified and $docIDs are empty, then all documents will be removed
     * @return Enterprise_Search_Model_Adapter_Abstract
     */
    public function deleteDocs($docIDs = array(), $queries = null)
    {
        $_deleteBySuffix = 'Ids';
        $params = array();
        if (!empty($docIDs)) {
            if (!is_array($docIDs)) {
                $docIDs = array($docIDs);
            }
            $params = $docIDs;
        } elseif (!empty($queries)) {
            if ($queries == 'all') {
                $queries = array('*:*');
            }
            if (!is_array($queries)) {
                $queries = array($queries);
            }
            $_deleteBySuffix = 'Queries';
            $params = $queries;
        }

        if ($params) {
            $deleteMethod = sprintf('deleteBy%s', $_deleteBySuffix);

            try {
                $this->_client->$deleteMethod($params);
            } catch (Exception $e) {
                $this->rollback();
                Mage::logException($e);
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
    public function getIdsByQuery($query, $params = array())
    {
        $params['fields'] = array('id');

        $result = $this->_search($query, $params);

        if (!isset($result['ids'])) {
            $result['ids'] = array();
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
    public function getStats($query, $params = array())
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
    public function search($query, $params = array())
    {
        return $this->_search($query, $params);
    }

    /**
     * Finalizes all add/deletes made to the index
     *
     * @return object|bool
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
     * @return object|bool
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
     */
    abstract protected function _connect($options = array());

    /**
     * Simple Search interface
     *
     * @param string $query
     * @param array $params
     */
    abstract protected function _search($query, $params = array());

    /**
     * Checks if Solr server is still up
     */
    abstract public function ping();

    /**
     * Retrieve language code by specified locale code if this locale is supported
     *
     * @param string $localeCode
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
        $_docs  = $realResponse->docs;
        if (!$_docs) {
            return array();
        }
        $this->_lastNumFound = (int)$realResponse->numFound;
        $result = array();
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
        $suggestions = array();

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
                usort($suggestions, array(get_class($this), 'sortSuggestions'));
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
     * @param   array $a
     * @param   array $b
     * @return  int
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
        $words = explode(' ', $text);
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

        $_index = array();
        if (!is_array($indexData)) {
            $indexData = array($indexData);
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
     * @link http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
     *
     * @param string $value
     * @return string
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
        if ($field == 'categories') {
            $fieldCondition = "(categories:{$value} OR show_in_categories:{$value})";
        } else {
            $fieldCondition = $field .':'. $value;
        }

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
        if(!is_object($object) && !is_array($object)){
            return $object;
        }
        if(is_object($object)){
            $object = get_object_vars($object);
        }

        return array_map(array($this, '_objectToArray'), $object);
    }

    /**
     * Convert facet results object to an array
     *
     * @param   object|array $object
     * @return  array
     */
    protected function _facetObjectToArray($object)
    {
        if(!is_object($object) && !is_array($object)){
            return $object;
        }

        if(is_object($object)){
            $object = get_object_vars($object);
        }

        $res = array();
        foreach ($object['facet_fields'] as $attr => $val) {
            foreach ($val as $key => $value) {
                $res[$attr][$key] = $value;
            }
        }

        foreach ($object['facet_queries'] as $attr => $val) {
            if (preg_match('/\(categories:(\d+) OR show_in_categories\:\d+\)/', $attr, $matches)) {
                $res['categories'][$matches[1]]    = $val;
            } else {
                $attrArray = explode(':', $attr);
                $res[$attrArray[0]][$attrArray[1]] = $val;
            }
        }

        return $res;
    }

    /**
     * Hold commit of changes for adapter
     *
     * @return Enterprise_Search_Model_Adapter_Abstract
     */
    public function holdCommit()
    {
        $this->_holdCommit = true;
        return $this;
    }

    /**
     * Allow changes commit for adapter
     *
     * @return Enterprise_Search_Model_Adapter_Abstract
     */
    public function allowCommit()
    {
        $this->_holdCommit = false;
        return $this;
    }

    /**
     * Define if third party search engine index needs optimization
     *
     * @param  bool $state
     * @return Enterprise_Search_Model_Adapter_Abstract
     */
    public function setIndexNeedsOptimization($state = true)
    {
        $this->_indexNeedsOptimization = (bool) $state;
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
}
