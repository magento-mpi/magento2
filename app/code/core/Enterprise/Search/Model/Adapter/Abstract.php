<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
        'name',
        'sku',
        'price',
        'description',
        'meta_keyword',
        'store_id',
        'categories',
        'visibility',
        'in_stock',
        //'fulltext',
        'score' //used to support sorting by this field
    );

    /**
     * Text fields which can store data differ in different languages
     *
     * @var array
     */
    protected $_searchTextFields = array(
        'name',
        'description',
        'meta_keyword',
        //'fulltext',
        'alphaNameSort' //used to implement more right sorting by name field
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
        'categories'
    );

    /**
     * Defines text type fields
     * Integer attributes are saved at metadata as text because in fact they are values for
     * options of select type inputs but their values are presented as text aliases
     *
     * @var array
     */
    protected $_textFieldTypes = array(
        'text',
        'varchar',
        'int'
    );

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
        'ignore_handler' => false
    );

    /**
     * Searchable attribute params
     *
     * @var array
     */
    protected $_indexableAttributeParams;

    /**
     * Retrieve attributes selected parameters
     *
     * @return array
     */
    protected function _getIndexableAttributeParams()
    {
        if (empty($this->_searchableAttributeParams)) {
            $entityTypeId = Mage::getSingleton('eav/config')
                ->getEntityType('catalog_product')
                ->getEntityTypeId();
            $items = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setEntityTypeFilter($entityTypeId)
                //->addVisibleFilter()
                ->addToIndexFilter()
                ->getItems();

            $this->_indexableAttributeParams = array();
            foreach ($items as $item) {
                $this->_indexableAttributeParams[$item->getAttributeCode()] = array(
                    'backendType'   => $item->getBackendType(),
                    'frontendInput' => $item->getFrontendInput(),
                    'searchWeight'  => $item->getSearchWeight()
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

        foreach ($docData as $entityId => $index) {
            $doc = new $this->_clientDocObjectName;

            /**
             * Set unique field
             */
            $index[self::getUniqueKey()] = $entityId . '|' . $index['store_id'];

            $index['id'] = $entityId;

            /**
             * Merge name field if it has multimple values
             */
            $index['name'] = $this->_implodeIndexData($index['name']);

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
            foreach ($index as $code => $value) {
                if (!empty($attributeParams[$code])) {
                    $weight = $attributeParams[$code]['searchWeight'];
                    $attributesWeights['fulltext' . $weight][] = $value;
                }
            }

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
                }
                else {
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
     * @return Enterprise_Search_Model_Adapter_Solr
     */
    public function addDocs($docs)
    {
        if (empty($docs)) {
            return $this;
        }
        $_docs = array();

        if (!is_array($docs)) {
            $docs = array($docs);
        }

        foreach ($docs as $doc) {
            if ($doc instanceof $this->_clientDocObjectName) {
               $_docs[] = $doc;
            }
        }

        if (empty($_docs)) {
            return $this;
        }

        try {
            $this->_client->ping();
            $response = $this->_client->addDocuments($_docs);
        }
        catch (Exception $e) {
            $this->rollback();
            Mage::logException($e);
        }
        $this->optimize();
        return $this;
    }

    /**
     * Remove documents from Solr index
     *
     * @param int|string|array $docIDs
     * @param string|array $queries if "all" specified and $docIDs are empty, then all documents will be removed
     * @return unknown
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
        }
        elseif (!empty($queries)) {
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
                $this->_client->ping();
                $response = $this->_client->$deleteMethod($params);
            }
            catch (Exception $e) {
                $this->rollback();
                Mage::logException($e);
            }
            $this->optimize();

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
        $ids = array();
        $params['fields'] = array('id');

        $_result = $this->_search($query, $params);

        if(!empty($_result['ids'])) {
            foreach ($_result['ids'] as $_id) {
                $ids[] = $_id['id'];
            }
        }

        return array($ids, $_result['facets']);
    }

    /**
     * Retrieve search suggestions by query
     *
     * @param string $query
     * @param array $params
     * @return boolean|string
     */
    public function getSuggestionsByQuery($query, $params = array(), $limit=false, $withResultsCounts=false)
    {
        return $this->_searchSuggestions($query, $params, $limit, $withResultsCounts);
    }

    /**
     * Retrieve data from solr facet search
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getFacetsByQuery($query, $params = array())
    {
        return $this->_searchFacets($query, $params);
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
     * @return object
     */
    public function commit()
    {
        return $this->_client->commit();
    }

    /**
     * Perform optimize operation
     * Same as commit operation, but also defragment the index for faster search performance
     *
     * @return object
     */
    public function optimize()
    {
        return $this->_client->optimize();
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
     * Connect to Search Engine Client by specified options
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
            $result[] = Mage::helper('enterprise_search')->objectToArray($doc);
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
        $arrayResponse = Mage::helper('enterprise_search')->objectToArray($response->spellcheck->suggestions);
        $suggestions = array();
        if (is_array($arrayResponse)) {
            foreach ($arrayResponse as $item) {
                if (isset($item['suggestion']) && is_array($item['suggestion'])) {
                    foreach ($item['suggestion'] as $suggestion) {
                        $suggestions[]=$suggestion;
                    }
                }
            }
        }
        // It is assumed that the frequency corresponds to the number of results
        if (count($suggestions) > 0) {
            usort($suggestions, array(get_class($this), "sortSuggestions"));
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
        return  Mage::helper('enterprise_search')->facetObjectToArray($response->facet_counts);
    }

    /**
     * Callback function for sort search suggestions
     *
     * @param array $a
     * @param array $b
     * @return boolean
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
        $_moreThenOneWord = sizeof(explode(' ', $text)) > 1;
        if ($_moreThenOneWord) {
            $text = $this->_phrase($text);
        }
        else {
            $text = $this->_escape($text);
        }
        return $text;
    }

    /**
     * Filter index data by common Solr metadata fields
     * Add language code suffix to text fields
     *
     * @deprecated after 1.8.0.0 - use $this->_prepareIndexData()
     *
     * @param array $data
     * @param string|null $localeCode
     * @return array
     * @see $this->_usedFields, $this->_searchTextFields
     */
    protected function _filterIndexData($data, $localeCode = null)
    {
        if (!is_array($data)) {
            return array();
        }
        if (empty($data)) {
            return array();
        }
        //$data = array_intersect_key($data, array_flip($this->_usedFields));
        foreach ($data as $code => $value) {
            if( !in_array($code, $this->_usedFields) && strpos($code, 'fulltext') !== 0 ) {
                unset($data[$code]);
            }
        }
        $languageCode = $this->_getLanguageCodeByLocaleCode($localeCode);
        if ($languageCode) {
            foreach ($data as $key => $value) {
                if ( in_array($key, $this->_searchTextFields) || strpos($key, 'fulltext') === 0) {
                    $data[$key . '_' . $languageCode] = $value;
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }

    /**
     * Prepare index data for using in Solr metadata
     * Add language code suffix to text fields
     * and type suffix for not text dynamic fields
     *
     * @see $this->_usedFields, $this->_searchTextFields
     *
     * @param array $data
     * @param array $attributesParams
     * @param string|null $localCode
     *
     * @return array
     */
    protected function _prepareIndexData($data, $attributesParams, $localeCode = null)
    {
        if (!is_array($data) || empty($data)) {
            return array();
        }

        $languageCode = $this->_getLanguageCodeByLocaleCode($localeCode);
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        foreach ($data as $key => $value) {

            if (in_array($key, $this->_usedFields) && !in_array($key, $this->_searchTextFields)) {
                continue;
            }
            elseif ($key == 'options') {
                unset($data[$key]);
                continue;
            }

            if (!array_key_exists($key, $attributesParams)) {
                $backendType = (substr($key, 0, 8) == 'fulltext') ? 'text' : null;
                $frontendInput = null;
            }
            else {
                $backendType = $attributesParams[$key]['backendType'];
                $frontendInput = $attributesParams[$key]['frontendInput'];
            }

            if ($frontendInput == 'multiselect') {
                if (!is_array($value)) {
                    $value = explode(' ', $value);
                }
                $data['attr_multi_'. $key] = $value;
                unset($data[$key]);
            } elseif (in_array($backendType, $this->_textFieldTypes) || in_array($key, $this->_searchTextFields)) {
                //for groupped products
                if (is_array($value)) {
                    $value = implode(' ', array_unique($value));
                }

                $data[$key . $languageSuffix] = $value;
                unset($data[$key]);
            }
            elseif ($backendType != 'static'
                && substr($key, 0, 6) != 'price_'
                && substr($key, 0, 18) != 'position_category_') {
                if ($backendType == 'datetime') {
                    $value = $this->_getSolrDate($data['store_id'], $value);
                }
                $data['attr_'. $backendType .'_'. $key] = $value;
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Retrieve default searchable fields
     *
     * @return array
     */
    public function getSearchTextFields()
    {
        return $this->_searchTextFields;
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
        $_index = array();
        if (is_string($indexData)) {
            return $indexData;
        }
        if (!is_array($indexData)) {
            $indexData = array($indexData);
        }
        if (!$indexData) {
            return '';
        }
        foreach ($indexData as $key => $value) {
            if (!is_array($value)) {
                $_index[] = $value;
            }
            else {
                $_index = array_merge($_index, $value);
            }
        }
        return implode($separator, $_index);
    }

    /**
     * Escape a value for special query characters such as ':', '(', ')', '*', '?', etc.
     *
     * @param string $value
     * @return string
     */
    public function _escape($value)
    {
        //list taken from http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
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
}
