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
 * Solr search engine adapter that perform raw queries to Solr server based on Conduit solr client library
 * and basic solr adapter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Adapter_HttpStream extends Enterprise_Search_Model_Adapter_Solr_Abstract
{
    /**
     * Object name used to create solr document object
     *
     * @var string
     */
    protected $_clientDocObjectName = 'Apache_Solr_Document';

    /**
     * Initialize connect to Solr Client
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        try {
            $this->_connect($options);
        } catch (Exception $e){
            Mage::logException($e);
        }
    }

    /**
     * Connect to Solr Client by specified options that will be merged with default
     *
     * @param array $options
     * @return Apache_Solr_Service
     */
    protected function _connect($options = array())
    {
        $helper = Mage::helper('enterprise_search');
        $def_options = array(
            'hostname' => $helper->getSolrConfigData('server_hostname'),
            'login'    => $helper->getSolrConfigData('server_username'),
            'password' => $helper->getSolrConfigData('server_password'),
            'port'     => $helper->getSolrConfigData('server_port'),
            'timeout'  => $helper->getSolrConfigData('server_timeout'),
            'path'     => $helper->getSolrConfigData('server_path')
        );
        $options = array_merge($def_options, $options);
        try {
            $this->_client = Mage::getSingleton('enterprise_search/client_solr', $options);
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
     *        'locale_code' - Locale code, it used to define what suffix is needed for text fields,
     *                        by which will be performed search request and sorting
     *
     * @see Enterprise_Search_Model_Adapter_HttpStream::_getLanguageCodeByLocaleCode()
     * @return array
     */
    protected function _search($query, $params = array())
    {
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

        $languageCode = $this->_getLanguageCodeByLocaleCode($params['locale_code']);
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';
        $searchParams = array();

        if (!is_array($_params['fields'])) {
            $_params['fields'] = array($_params['fields']);
        }

        if (!is_array($_params['solr_params'])) {
            $_params['solr_params'] = array($_params['solr_params']);
        }

        /**
         * Add sort fields
         */
        $sortFields = $this->_prepareSortFields($_params['sort_by']);
        foreach ($sortFields as $sortField) {
            $searchParams['sort'][] = $sortField['sortField'] . ' ' . $sortField['sortType'];
        }

        /**
         * Fields to retrieve
         */
        if (!empty($_params['fields'])) {
            $searchParams['fl'] = implode(',', $_params['fields']);
        }

        /**
         * Now supported search only in fulltext and name fields based on dismax requestHandler.
         * Using dismax requestHandler for each language make matches in name field
         * are much more significant than matches in fulltext field.
         */
        if ($_params['ignore_handler'] !== true) {
            $_params['solr_params']['qt'] = 'magento' . $languageSuffix;
        }

        if (isset($params['solr_params']['facet']) && $params['solr_params']['facet'] == 'on') {
            $searchParams += $this->_prepareFacetConditions($params['facet']);
        }

        /**
         * Specific Solr params
         */
        if (!empty($_params['solr_params'])) {
            foreach ($_params['solr_params'] as $name => $value) {
                $searchParams[$name] = $value;
            }
        }

        $searchParams['fq'] = $this->_prepareFilters($params['filters']);

        /**
         * Store filtering
         */
        if ($_params['store_id'] > 0) {
            $searchParams['fq'][] = 'store_id:' . $_params['store_id'];
        }
        if (!Mage::helper('cataloginventory')->isShowOutOfStock()) {
            $searchParams['fq'][] = 'in_stock:true';
        }

        $searchParams['fq'] = implode(' AND ', $searchParams['fq']);

        try {
            $this->ping();
            $response = $this->_client->search($searchConditions, $offset, $limit, $searchParams);
            $data = json_decode($response->getRawResponse());

            $result = array('ids' => $this->_prepareQueryResponse($data));

            if (isset($params['solr_params']['facet']) && $params['solr_params']['facet'] == 'on'){
                $result['facets'] = $this->_prepareFacetsQueryResponse($data);
            } else {
                $result['facets'] = array();
            }

            return $result;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Simple Search suggestions interface
     *
     * @param string $query The raw query string
     * @return boolean|string
     */
    public function _searchSuggestions($query, $params=array(), $limit=false, $withResultsCounts=false)
    {
        $query = $this->_prepareQueryText($query);
        if (!$query) {
            return array();
        }

        $_params = array();

        $languageCode = $this->_getLanguageCodeByLocaleCode($params['locale_code']);
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        $_params['solr_params'] = array (
            'spellcheck'                 => 'true',
            'spellcheck.collate'         => 'true',
            'spellcheck.dictionary'      => 'magento_spell' . $languageSuffix,
            'spellcheck.extendedResults' => 'true'
        );

        try {
            $this->ping();
            $response = $this->_client->searchSuggestions($query, $_params['solr_params']);
            $result = $this->_prepareSuggestionsQueryResponse( json_decode($response->getRawResponse()) );

            if ($limit) {
                $result = array_slice($result, 0, $limit);
            }
            // Calc results count for each suggestion
            if ($withResultsCounts) {
                $tmp = $this->_lastNumFound; //Temporary store value for main search query
                foreach ($result as $key => $item) {
                    $this->search($item['word'], $params);
                    $result[$key]['num_results'] = $this->_lastNumFound;
                }
                $this->_lastNumFound = $tmp; //Revert store value for main search query
            }
            return $result;
        } catch (Exception $e) {
            Mage::logException($e);
            return array();
        }
    }
}
