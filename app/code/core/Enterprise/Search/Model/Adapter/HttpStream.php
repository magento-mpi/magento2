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

class Enterprise_Search_Model_Adapter_HttpStream extends Enterprise_Search_Model_Adapter_Abstract
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
        }
        catch (Exception $e){
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

        $searchParams = array();
        $languageCode = $this->_getLanguageCodeByLocaleCode($params['locale_code']);

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
            $_params['sort_by'] = array(array($_params['sort_by'] => 'asc'));
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
                if (in_array($sortField, $this->_searchTextFields) && $languageCode) {
                    $sortField = $sortField . '_' . $languageCode;
                }
                $sortType = trim(strtolower($sortType)) == 'desc' ? 'desc' : 'asc';
                $searchParams['sort'][] = $sortField . ' ' . $sortType;
            }
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
        $_params['solr_params']['qt'] = 'magento';
        if ($languageCode) {
            $_params['solr_params']['qt'] .= '_' . $languageCode;
        }

        /**
         * Specific Solr params
         */
        if (!empty($_params['solr_params'])) {
            foreach ($_params['solr_params'] as $name => $value) {
                $searchParams[$name] = $value;
            }
        }

        /**
         * Store filtering
         */
        if ($_params['store_id'] > 0) {
            $searchParams['fq'] = 'store_id:' . $_params['store_id'];
        }
        if (!Mage::helper('cataloginventory')->isShowOutOfStock()) {
            $searchParams['fq'] .= ' AND in_stock:true';
        }

        try {
            $this->_client->ping();
            $response = $this->_client->search($query, $offset, $limit, $searchParams);
            $data = json_decode($response->getRawResponse());
            return $this->_prepareQueryResponse($data);
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Checks if Solr server is still up
     *
     * @return bool
     */
    public function ping()
    {
        return $this->_client->ping();
    }

    /**
     * Retrieve language code by specified locale code if this locale is supported
     *
     * @param string $localeCode
     * @return false|string
     */
    protected function _getLanguageCodeByLocaleCode($localeCode)
    {
        $localeCode = (string)$localeCode;
        if (!$localeCode) {
            return false;
        }
        $languages = Mage::helper('enterprise_search')->getSolrSupportedLanguages();
        foreach ($languages as $code => $locales) {
            if (is_array($locales)) {
                if (in_array($localeCode, $locales)) {
                    return $code;
                }
            }
            elseif ($localeCode == $locales) {
                return $code;
            }
        }
        return false;
    }
}
