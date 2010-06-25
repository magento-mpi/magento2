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
class Enterprise_Search_Model_Adapter_PhpExtension extends Enterprise_Search_Model_Adapter_Solr_Abstract
{
    /**
     * Object name used to create solr document object
     *
     * @var string
     */
    protected $_clientDocObjectName = 'SolrInputDocument';

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
        catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable to perform search because of search engine missed configuration.'));
        }
    }

    /**
     * Connect to Solr Client by specified options that will be merged with default
     *
     * @param array $options
     * @return SolrClient
     */
    protected function _connect($options = array())
    {
        $helper = Mage::helper('enterprise_search');
        $def_options = array
        (
            'hostname' => $helper->getSolrConfigData('server_hostname'),
            'login'    => $helper->getSolrConfigData('server_username'),
            'password' => $helper->getSolrConfigData('server_password'),
            'port'     => $helper->getSolrConfigData('server_port'),
            'timeout'  => $helper->getSolrConfigData('server_timeout'),
            'path'     => $helper->getSolrConfigData('server_path')
        );
        $options = array_merge($def_options, $options);
        try {
            $this->_client = new SolrClient($options);
        }
        catch (Exception $e)
        {
            Mage::logException($e);
        }
        return $this->_client;
    }

    /**
     * Simple Search interface
     *
     * @param string|array $query   The raw query string
     * @param array $params Params  can be specified like this:
     *        'offset'            - The starting offset for result documents
     *        'limit              - The maximum number of result documents to return
     *        'sort_by'           - Sort field, can be just sort field name (and asceding order will be used by default),
     *                              or can be an array of arrays like this: array('sort_field_name' => 'asc|desc')
     *                              to define sort order and sorting fields.
     *                              If sort order not asc|desc - asceding will used by default
     *        'fields'            - Fields names which are need to be retrieved from found documents
     *        'solr_params'       - Key / value pairs for other query parameters (see Solr documentation),
     *                              use arrays for parameter keys used more than once (e.g. facet.field)
     *        'locale_code'       - Locale code, it used to define what suffix is needed for text fields,
     *                              by which will be performed search request and sorting
     *        'ignore_handler'    - Flag that allows to ignore handler (qt) and make multifield search
     *
     * @see Enterprise_Search_Model_Adapter_HttpStream::_getLanguageCodeByLocaleCode()
     * @return array
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

        $searchConditions = $this->prepareSearchConditions($query);

        if (!$searchConditions) {
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
        $languageCode = $this->_getLanguageCodeByLocaleCode($params['locale_code']);
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        $solrQuery = new SolrQuery();
        $solrQuery->setStart($offset)->setRows($limit);



        $solrQuery->setQuery($searchConditions);

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
            elseif ($_params['sort_by'] == 'name') {
                $_params['sort_by'] = 'alphaNameSort';
            }
            elseif ($_params['sort_by'] == 'position') {
                $sortField = 'position_category_' . Mage::registry('current_category')->getId();
            }
            elseif ($_params['sort_by'] == 'price') {
                $websiteId       = Mage::app()->getStore()->getWebsiteId();
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

                $_params['sort_by'] = 'price_'. $customerGroupId .'_'. $websiteId;
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
            elseif ($sortField == 'position') {
                $sortField = 'position_category_' . Mage::registry('current_category')->getId();
            }
            elseif ($sortField == 'price') {
                $websiteId       = Mage::app()->getStore()->getWebsiteId();
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

                $sortField = 'price_'. $customerGroupId .'_'. $websiteId;
            }
            else {
                $sortField = $this->getAttributeSolrFieldName($sortField);
            }

            $sortType = trim(strtolower($sortType)) == 'desc' ? SolrQuery::ORDER_DESC : SolrQuery::ORDER_ASC;
            $solrQuery->addSortField($sortField, $sortType);
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
         * Now supported search only in fulltext and name fields based on dismax requestHandler (named as magento_lng).
         * Using dismax requestHandler for each language make matches in name field
         * are much more significant than matches in fulltext field.
         */
        if ($_params['ignore_handler'] !== true) {
            $_params['solr_params']['qt'] = 'magento' . $languageSuffix;
        }

        if (isset($params['solr_params']['facet']) && $params['solr_params']['facet'] == 'on') {
            $_params['solr_params']['facet'] = 'on';
            if (isset($params['facet'])) {
                foreach ($params['facet'] as $facetField => $facetFieldConditions) {
                    if (empty($facetFieldConditions)) {
                        $_params['solr_params']['facet.field'][] = $facetField;
                    }
                    else {
                        foreach ($facetFieldConditions as $facetCondition) {
                            if (is_array($facetCondition) && isset($facetCondition['from']) && isset($facetCondition['to'])) {
                                $from = (isset($facetCondition['from']) && !empty($facetCondition['from']))
                                    ? $this->_prepareQueryText($facetCondition['from'])
                                    : '*';
                                $to = (isset($facetCondition['to']) && !empty($facetCondition['to']))
                                    ? $this->_prepareQueryText($facetCondition['to'])
                                    : '*';
                                $fieldCondition = "$facetField:[$from TO $to]";
                            }
                            else {
                                $facetCondition = $this->_prepareQueryText($facetCondition);
                                $fieldCondition = $this->_prepareFieldCondition($facetField, $facetCondition);
                            }

                            $_params['solr_params']['facet.query'][] = $fieldCondition;
                        }
                    }
                }
            }
        }

        /**
         * Specific Solr params
         */
        if (!empty($_params['solr_params'])) {
            foreach ($_params['solr_params'] as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $solrQuery->addParam($name, $val);
                    }
                }
                else {
                    $solrQuery->addParam($name, $value);
                }
            }
        }

        if (!empty($params['filters'])) {
            foreach ($params['filters'] as $field => $value) {
                if (is_array($value)) {
                    if ($field == 'price' || isset($value['from']) || isset($value['to'])) {
                        $from = (isset($value['from']) && !empty($value['from'])) ? $this->_prepareQueryText($value['from']) : '*';
                        $to = (isset($value['to']) && !empty($value['to'])) ? $this->_prepareQueryText($value['to']) : '*';
                        $fieldCondition = "$field:[$from TO $to]";
                    }
                    else {
                        $fieldCondition = array();
                        foreach ($value as $part) {
                            $part = $this->_prepareQueryText($part);
                            $fieldCondition[] = $this->_prepareFieldCondition($field, $part);
                        }
                        $fieldCondition = '(' . implode(' OR ', $fieldCondition) . ')';
                    }
                }
                else {
                    $value = $this->_prepareQueryText($value);
                    $fieldCondition = $this->_prepareFieldCondition($field, $value);
                }

                $solrQuery->addFilterQuery($fieldCondition);
            }
        }

        $this->_client->setServlet(SolrClient::SEARCH_SERVLET_TYPE, 'select');
        /**
         * Store filtering
         */
        if ($_params['store_id'] > 0) {
            $solrQuery->addFilterQuery('store_id:' . $_params['store_id']);
        }
        if (!Mage::helper('cataloginventory')->isShowOutOfStock()) {
            $solrQuery->addFilterQuery('in_stock:true');
        }

        try {
            $this->ping();
            $response = $this->_client->query($solrQuery);

            $result = array('ids' => $this->_prepareQueryResponse($response->getResponse()));

            if (isset($params['solr_params']['facet']) && $params['solr_params']['facet'] == 'on'){
                $result['facets'] = $this->_prepareFacetsQueryResponse($response->getResponse());
            } else {
                $result['facets'] = array();
            }

            return $result;
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Simple Search suggestions interface
     *
     * @param string $query The raw query string
     * @return boolean|string
     */
    public function _searchSuggestions($query, $params = array(), $limit = false, $withResultsCounts = false)
    {

        /**
         * @see self::_search()
         */
        if ((int)('1' . str_replace('.', '', solr_get_version())) < 1099) {
            $this->_connect();
        }

        $query = $this->_escapePhrase($query);

        if (!$query) {
            return false;
        }
        $_params = array();


        $languageCode = $this->_getLanguageCodeByLocaleCode($params['locale_code']);
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        $solrQuery = new SolrQuery($query);

        /**
         * Now supported search only in fulltext and name fields based on dismax requestHandler (named as magento_lng).
         * Using dismax requestHandler for each language make matches in name field
         * are much more significant than matches in fulltext field.
         */

        $_params['solr_params'] = array (
            'spellcheck'                 => 'true',
            'spellcheck.collate'         => 'true',
            'spellcheck.dictionary'      => 'magento_spell' . $languageSuffix,
            'spellcheck.extendedResults' => 'true'
        );

        /**
         * Specific Solr params
         */
        if (!empty($_params['solr_params'])) {
            foreach ($_params['solr_params'] as $name => $value) {
                $solrQuery->setParam($name, $value);
            }
        }

        $this->_client->setServlet(SolrClient::SEARCH_SERVLET_TYPE, 'spell');
        /**
         * Store filtering
         */
        if (!empty($params['store_id'])) {
            $solrQuery->addFilterQuery('store_id:' . $params['store_id']);
        }
        if (!Mage::helper('cataloginventory')->isShowOutOfStock()) {
            $solrQuery->addFilterQuery('in_stock:true');
        }

        try {
            $this->ping();
            $response = $this->_client->query($solrQuery);
            $result = $this->_prepareSuggestionsQueryResponse($response->getResponse());
            $resultLimit = array();
            // Calc results count for each suggestion
            if ($withResultsCounts && $limit) {
                $tmp = $this->_lastNumFound; //Temporary store value for main search query
                foreach ($result as $key => $item) {
                    $this->search($item['word'], $params);
                    if ($this->_lastNumFound) {
                        $result[$key]['num_results'] = $this->_lastNumFound;
                        $resultLimit[]= $result[$key];
                        $limit--;
                    }
                    if ($limit <= 0) {
                        break;
                    }
                }
                $this->_lastNumFound = $tmp; //Revert store value for main search query
            } else {
                $resultLimit = array_slice($result, 0, $limit);
            }
            return $resultLimit;
        }
        catch (Exception $e) {
            Mage::logException($e);
            return array();
        }
    }

    /**
     * Checks if Solr server is still up
     *
     * @return bool
     */
    public function ping()
    {
        Mage::helper('enterprise_search')->getSolrSupportedLanguages();
        return parent::ping();
    }
}
