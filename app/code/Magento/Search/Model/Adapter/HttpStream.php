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
 * Solr search engine adapter that perform raw queries to Solr server based on Conduit solr client library
 * and basic solr adapter
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Model_Adapter_HttpStream extends Magento_Search_Model_Adapter_Solr_Abstract
    implements Magento_Search_Model_AdapterInterface
{
    /**
     * Object name used to create solr document object
     *
     * @var string
     */
    protected $_clientDocObjectName = 'Apache_Solr_Document';

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
     * @return array
     */
    /**
     * Catalog inventory data
     *
     * @var Magento_CatalogInventory_Helper_Data
     */
    protected $_ctlgInventData = null;

    /**
     * Initialize connect to Solr Client
     *
     * @param Magento_CatalogInventory_Helper_Data $ctlgInventData
     * @param Magento_Search_Model_Client_FactoryInterface $clientFactory
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Search_Helper_ClientInterface $clientHelper
     * @param  $options
     */
    public function __construct(
        Magento_CatalogInventory_Helper_Data $ctlgInventData,
        Magento_Search_Model_Client_FactoryInterface $clientFactory,
        Magento_Core_Model_Logger $logger,
        Magento_Search_Helper_ClientInterface $clientHelper,
        $options = array()
    ) {
        $this->_ctlgInventData = $ctlgInventData;
        parent::__construct($clientFactory, $logger, $clientHelper, $options);
    }

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

        $offset = (isset($_params['offset'])) ? (int) $_params['offset'] : 0;
        $limit  = (isset($_params['limit']))
            ? (int) $_params['limit']
            : Magento_Search_Model_Adapter_Solr_Abstract::DEFAULT_ROWS_LIMIT;

        $languageSuffix = $this->_getLanguageSuffix($params['locale_code']);
        $searchParams   = array();

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
        if ($limit && !empty($_params['fields'])) {
            $searchParams['fl'] = implode(',', $_params['fields']);
        }

        /**
         * Now supported search only in fulltext and name fields based on dismax requestHandler (named as magento_lng).
         * Using dismax requestHandler for each language make matches in name field
         * are much more significant than matches in fulltext field.
         */
        if ($_params['ignore_handler'] !== true) {
            $_params['solr_params']['qt'] = 'magento' . $languageSuffix;
        }

        /**
         * Facets search
         */
        $useFacetSearch = (isset($params['solr_params']['facet']) && $params['solr_params']['facet'] == 'on');
        if ($useFacetSearch) {
            $searchParams += $this->_prepareFacetConditions($params['facet']);
        }

        /**
         * Suggestions search
         */
        $useSpellcheckSearch = isset($params['solr_params']['spellcheck'])
            && $params['solr_params']['spellcheck'] == 'true';

        if ($useSpellcheckSearch) {
            if (isset($params['solr_params']['spellcheck.count'])
                && (int) $params['solr_params']['spellcheck.count'] > 0
            ) {
                $spellcheckCount = (int) $params['solr_params']['spellcheck.count'];
            } else {
                $spellcheckCount = self::DEFAULT_SPELLCHECK_COUNT;
            }

            $_params['solr_params'] += array(
                'spellcheck.collate'         => 'true',
                'spellcheck.dictionary'      => 'magento_spell' . $languageSuffix,
                'spellcheck.extendedResults' => 'true',
                'spellcheck.count'           => $spellcheckCount
            );
        }

        /**
         * Specific Solr params
         */
        if (!empty($_params['solr_params'])) {
            foreach ($_params['solr_params'] as $name => $value) {
                $searchParams[$name] = $value;
            }
        }

        $searchParams['fq'] = $this->_prepareFilters($_params['filters']);

        /**
         * Store filtering
         */
        if ($_params['store_id'] > 0) {
            $searchParams['fq'][] = 'store_id:' . $_params['store_id'];
        }
        if (!$this->_ctlgInventData->isShowOutOfStock()) {
            $searchParams['fq'][] = 'in_stock:true';
        }

        $searchParams['fq'] = implode(' AND ', $searchParams['fq']);

        try {
            $this->ping();
            $response = $this->_client->search(
                $searchConditions, $offset, $limit, $searchParams, Apache_Solr_Service::METHOD_POST
            );
            $data = json_decode($response->getRawResponse());

            if (!isset($params['solr_params']['stats']) || $params['solr_params']['stats'] != 'true') {
                if ($limit > 0) {
                    $result = array('ids' => $this->_prepareQueryResponse($data));
                }

                /**
                 * Extract facet search results
                 */
                if ($useFacetSearch) {
                    $result['faceted_data'] = $this->_prepareFacetsQueryResponse($data);
                }

                /**
                 * Extract suggestions search results
                 */
                if ($useSpellcheckSearch) {
                    $resultSuggestions = $this->_prepareSuggestionsQueryResponse($data);
                    /* Calc results count for each suggestion */
                    if (isset($params['spellcheck_result_counts']) && $params['spellcheck_result_counts']
                        && count($resultSuggestions)
                        && $spellcheckCount > 0
                    ) {
                        /* Temporary store value for main search query */
                        $tmpLastNumFound = $this->_lastNumFound;

                        unset($params['solr_params']['spellcheck']);
                        unset($params['solr_params']['spellcheck.count']);
                        unset($params['spellcheck_result_counts']);

                        $suggestions = array();
                        foreach ($resultSuggestions as $key => $item) {
                            $this->_lastNumFound = 0;
                            $this->search($item['word'], $params);
                            if ($this->_lastNumFound) {
                                $resultSuggestions[$key]['num_results'] = $this->_lastNumFound;
                                $suggestions[] = $resultSuggestions[$key];
                                $spellcheckCount--;
                            }
                            if ($spellcheckCount <= 0) {
                                break;
                            }
                        }

                        /* Return store value for main search query */
                        $this->_lastNumFound = $tmpLastNumFound;
                    } else {
                        $suggestions = array_slice($resultSuggestions, 0, $spellcheckCount);
                    }
                    $result['suggestions_data'] = $suggestions;
                }
            } else {
                $result = $this->_prepateStatsQueryResponce($data);
            }

            return $result;
        } catch (Exception $e) {
            $this->_log->logException($e);
        }
    }

    /**
     * Checks if Solr server is still up
     *
     * @return bool
     */
    public function ping()
    {
        return parent::ping();
    }

    /**
     * Retrieve attribute solr field name
     *
     * @param   Magento_Catalog_Model_Resource_Eav_Attribute|string $attribute
     * @param   string $target - default|sort|nav
     *
     * @return  string|bool
     */
    public function getSearchEngineFieldName($attribute, $target = 'default')
    {
        return parent::getSearchEngineFieldName($attribute, $target);
    }
}
