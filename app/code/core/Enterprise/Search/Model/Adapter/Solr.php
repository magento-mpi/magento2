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
 * Solr search engine adapter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Adapter_Solr {

    /**
     * Field to use to determine and enforce document uniqueness
     *
     */
    const UNIQUE_KEY = 'unique';

    /**
     * Store Solr Client instance
     *
     * @var SolrClient
     */
    protected $_client = null;

    /**
     * Store last search query number of found results
     *
     * @var int
     */
    protected $_lastNumFound = 0;

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
        'fulltext',
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
        'fulltext',
        'alphaNameSort' //used to implement more right sorting by name field
    );

    /**
     * Search query params with their default values
     *
     * @var array
     */
    protected $_defaultQueryParams = array(
        'offset'        => 0,
        'limit'         => 100,
        'sort_by'       => array(array('score' => 'desc')),
        'store_id'      => null,
        'lang_code'     => null,
        'fields'        => array(),
        'solr_params'   => array(),
    );

    /**
     * Initialize connect to Solr Client
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        try {
            if (!extension_loaded('solr')) {
                throw new Exception('Solr extension not enabled!');
            }
            $this->_connect($options);
        }
        catch (Exception $e){
            Mage::logException($e);
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable to perform search because of search engine missed configuration.'));
        }
    }

    /**
     * Retrieve information from Solr search engine configuration
     *
     * @param string $field
     * @param int $storeId
     * @return string|int
     */
    public function getConfigData($field, $storeId = null)
    {
        $path = 'catalog/search_engine/'.$field;
        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * Create Solr Input Documents by specified data
     *
     * @param array $docData
     * @param string|null $languageCode
     * @return array
     */
    public function prepareDocs($docData, $languageCode = null)
    {
        if (!is_array($docData)) {
            return array();
        }
        if (empty($docData)) {
            return array();
        }
        $docs = array();
        foreach ($docData as $entityId => $index) {
            $doc = new SolrInputDocument();

            /**
             * Set unique field
             */
            $index[self::getUniqueKey()] = $entityId . '|' . $index['store_id'];

            $index['id'] = $entityId;

            /**
             * Merge all attributes to fulltext field
             */
            $index['fulltext'] = $this->_implodeIndexData($index);

            /**
             * Merge name field if it has multimple values
             */
            $index['name'] = $this->_implodeIndexData($index['name']);

            $index = $this->_filterIndexData($index, $languageCode);
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
            if ($doc instanceof SolrInputDocument) {
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
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable to add documents to search engine index.'));
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
                Mage::throwException(Mage::helper('enterprise_search')->__('Unable to delete documents from search engine index.'));
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

        foreach ($_result as $_id) {
            $ids[] = $_id['id'];
        }
        return $ids;
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
     * @return SolrUpdateResponse
     */
    public function commit()
    {
        $response = $this->_client->commit();
        return $response->getResponse();
    }

    /**
     * Perform optimize operation
     * Same as commit operation, but also defragment the index for faster search performance
     *
     * @return SolrUpdateResponse
     */
    public function optimize()
    {
        $response = $this->_client->optimize();
        return $response->getResponse();
    }

    /**
     * Rollbacks all add/deletes made to the index since the last commit
     *
     * @return SolrUpdateResponse
     */
    public function rollback()
    {
        $response = $this->_client->rollback();
        return $response->getResponse();
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
     * Define if solr need to store and use in search separate fields by languages codes
     *
     * @return bool
     */
    public function getIsUseLanguageFields()
    {
        return (bool)$this->getConfigData('use_language_fields');
    }

    /**
     * Connect to Solr Client by specified options that will be merged with default
     *
     * @param array $options
     * @return object
     */
    protected function _connect($options = array())
    {
        $def_options = array
        (
            'hostname' => $this->getConfigData('server_hostname'),
            'login'    => $this->getConfigData('server_username'),
            'password' => $this->getConfigData('server_password'),
            'port'     => $this->getConfigData('server_port'),
            'timeout'  => $this->getConfigData('server_timeout'),
            'path'  => $this->getConfigData('server_path')
        );
        $options = array_merge($def_options, $options);
        try {
        $this->_client = new SolrClient($options);
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable to connect to the search client.'));
        }
        return $this->_client;
    }

    /**
     * Simple Search interface
     *
     * @param string $query The raw query string
     * @param array $params Params can be specified like this:
     *        'offset'      - The starting offset for result documents
     *        'limit        - The maximum number of result documents to return
     *        'sort_by'     - Sort field, can be just sort field name (and asceding order will be used by default),
     *                        or can be an array of arrays like this: array('sort_field_name' => 'asc|desc')
     *                        to define sort order and sorting fields.
     *                        If sort order not asc|desc - asceding will used by default
     *        'fields'      - Fields names which are need to be retrieved from found documents
     *        'solr_params' - Key / value pairs for other query parameters (see Solr documentation),
     *                        use arrays for parameter keys used more than once (e.g. facet.field)
     *        'lang_code'   - Language code, that will be used as suffix for text fields,
     *                        by whish will be performed search request and sorting
     *
     *
     * @return SolrQueryResponse
     */
    protected function _search($query, $params = array())
    {
        /**
         * Hard code to prevent Solr bug:
         * Bug #17009 Creating two SolrQuery objects leads to wrong query value
         * @see http://pecl.php.net/bugs/bug.php?id=17009&edit=1
         * @see http://svn.php.net/viewvc?view=revision&revision=293379
         */
        if ((int)('1' . str_replace('.', '', solr_get_version())) < 1099) {
            $this->_connect();
        }


        $query = $this->_prepareQueryText($query);
        if (!$query) {
            return array();
        }
        $_params = $this->_defaultQueryParams;
        if (is_array($params) && !empty($params)) {
            $_params = array_intersect_key($params, $_params) + array_diff_key($_params, $params);
        }
        $offset = (int)$_params['offset'];
        $limit  = (int)$_params['limit'];

        if (!$limit) {
            $limit = 100;
        }

        /**
         * Now supported search only in fulltext field
         * By default in Solr  set <defaultSearchField> is "fulltext"
         * When language fields need to be used, then perform search in appropriate field
         */
        if ($this->getIsUseLanguageFields() && $params['lang_code']) {
            $query = 'fulltext_' . $params['lang_code'] . ':' . $query;
        }

        $solrQuery = new SolrQuery($query);
        $solrQuery->setStart($offset)->setRows($limit);

        if (!is_array($_params['fields'])) {
            $_params['fields'] = array($_params['fields']);
        }

        if (!is_array($_params['solr_params'])){
            $_params['solr_params'] = array($_params['solr_params']);
        }

        /**
         * Support specifing sort by field as only string name of field
         */
        if (!empty($_params['sort_by']) && !is_array($_params['sort_by'])) {
            if ($_params['sort_by'] == 'relevance') {
                $_params['sort_by'] = 'score';
            }
            if ($_params['sort_by'] == 'name') {
                $_params['sort_by'] = 'alphaNameSort';
            }
            $_params['sort_by'] = array(array($_params['sort_by'] => SolrQuery::ORDER_ASC));
        }

        /**
         * Add sort fields
         */
        foreach ($_params['sort_by'] as $_key => $sort) {
            $_sort = each($sort);
            $sortField = $_sort['key'];
            $sortType = $_sort['value'];
            if ($sortField == 'relevance') {
                $sortField = 'score';
            }
            if (in_array($sortField, $this->_usedFields)) {
                if ($sortField == 'name') {
                    $sortField = 'alphaNameSort';
                }
                if (in_array($sortField, $this->_searchTextFields) && $this->getIsUseLanguageFields() && $params['lang_code']) {
                    $sortField = $sortField . '_' . $params['lang_code'];
                }
                $sortType = trim(strtolower($sortType)) == 'desc' ? SolrQuery::ORDER_DESC : SolrQuery::ORDER_ASC;
                 $solrQuery->addSortField($sortField, $sortType);
            }
        }

        /**
         * Fields to retrieve
         */
        if (!empty($_params['fields'])) {
            foreach ($_params['fields'] as $field) {
                $solrQuery->addField($field);
            }
        }

        /**
         * Specific Solr params
         */
        if (!empty($_params['solr_params'])) {
            foreach ($_params['solr_params'] as $name => $value) {
                $solrQuery->setParam($name, $value);
            }
        }

        /**
         * Store filtering
         */
        if ($_params['store_id'] > 0) {
            $solrQuery->addFilterQuery('store_id:' . $_params['store_id']);
        }

        try {
            $this->_client->ping();
            $response = $this->_client->query($solrQuery);
            return $this->_prepareQueryResponse($response);
        }
        catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable perform search request.'));
        }

    }

    /**
     * Convert Solr Query Response found documents to an array
     *
     * @param SolrQueryResponse $response
     * @return array
     */
    protected function _prepareQueryResponse(SolrQueryResponse $response)
    {
        $realResponse = $response->getResponse()->response;
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
     * @param array $data
     * @param string|null $languageCode
     * @return array
     * @see $this->_usedFields, $this->_searchTextFields
     */
    protected function _filterIndexData($data, $languageCode = null)
    {
        if (!is_array($data)) {
            return array();
        }
        if (empty($data)) {
            return array();
        }
        $data = array_intersect_key($data, array_flip($this->_usedFields));
        if ($this->getIsUseLanguageFields() && $languageCode) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->_searchTextFields)) {
                    $data[$key . '_' . $languageCode] = $value;
                    unset($data[$key]);
                }
            }
        }
        return $data;
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