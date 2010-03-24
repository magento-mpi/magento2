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
     * Retrieve information from Solr search engine configuration
     *
     * @param string $field
     * @param int $storeId
     * @return string|int
     */
    public function getConfigData($field, $storeId = null)
    {
        $path = 'catalog/search/solr_'.$field;
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
            $doc = new $this->_clientDocObjectName;

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
     * Define if solr need to store and use in search separate fields by languages codes
     *
     * @return bool
     */
    public function getIsUseLanguageFields()
    {
        return (bool)$this->getConfigData('use_language_fields');
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