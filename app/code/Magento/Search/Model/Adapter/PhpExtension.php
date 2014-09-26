<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Adapter;

/**
 * Solr search engine adapter
 */
class PhpExtension extends \Magento\Search\Model\Adapter\Solr\AbstractSolr implements
    \Magento\Search\Model\AdapterInterface
{
    /**
     * Object name used to create solr document object
     *
     * @var string
     */
    protected $_clientDocObjectName = 'SolrInputDocument';

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Search\Model\Resource\Index $resourceIndex
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\Collection $attributeCollection
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Search\Model\Factory\Factory $searchFactory
     * @param \Magento\Search\Helper\ClientInterface $clientHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param array $options
     * @throws \Magento\Framework\Model\Exception
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Search\Model\Resource\Index $resourceIndex,
        \Magento\Catalog\Model\Resource\Product\Attribute\Collection $attributeCollection,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Search\Model\Factory\Factory $searchFactory,
        \Magento\Search\Helper\ClientInterface $clientHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        $options = array()
    ) {
        if (!extension_loaded('solr')) {
            throw new \Magento\Framework\Model\Exception('Solr extension not enabled!');
        }
        parent::__construct(
            $customerSession,
            $resourceIndex,
            $attributeCollection,
            $logger,
            $storeManager,
            $cache,
            $eavConfig,
            $searchFactory,
            $clientHelper,
            $registry,
            $scopeConfig,
            $dateTime,
            $localeResolver,
            $localeDate,
            $options
        );
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
     * @return array
     */
    protected function _search($query, $params = array())
    {
        /**
         * Hard code to prevent Solr bug:
         * Bug #17009 Creating two SolrQuery objects leads to wrong query value
         * @link http://pecl.php.net/bugs/bug.php?id=17009&edit=1
         * @link http://svn.php.net/viewvc?view=revision&revision=293379
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

        $offset = isset($_params['offset']) ? (int)$_params['offset'] : 0;
        $limit = isset(
            $_params['limit']
        ) ? (int)$_params['limit'] : \Magento\Search\Model\Adapter\Solr\AbstractSolr::DEFAULT_ROWS_LIMIT;

        /**
         * Now supported search only in fulltext field
         * By default in Solr  set <defaultSearchField> is "fulltext"
         * When language fields need to be used, then perform search in appropriate field
         */
        $languageSuffix = $this->_getLanguageSuffix($params['locale_code']);

        $solrQuery = new SolrQuery();
        $solrQuery->setStart($offset)->setRows($limit);

        $solrQuery->setQuery($searchConditions);

        if (!is_array($_params['fields'])) {
            $_params['fields'] = array($_params['fields']);
        }

        if (!is_array($_params['solr_params'])) {
            $_params['solr_params'] = array($_params['solr_params']);
        }

        /**
         * Add sort fields
         */
        if ($limit > 1) {
            $sortFields = $this->_prepareSortFields($_params['sort_by']);
            foreach ($sortFields as $sortField) {
                $sortField['sortType'] = $sortField['sortType'] ==
                    'desc' ? SolrQuery::ORDER_DESC : SolrQuery::ORDER_ASC;
                $solrQuery->addSortField($sortField['sortField'], $sortField['sortType']);
            }
        }

        /**
         * Fields to retrieve
         */
        if ($limit && !empty($_params['fields'])) {
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

        /**
         * Facets search
         */
        $useFacetSearch = isset($params['solr_params']['facet']) && $params['solr_params']['facet'] == 'on';
        if ($useFacetSearch) {
            $_params['solr_params'] += $this->_prepareFacetConditions($params['facet']);
        }

        /**
         * Suggestions search
         */
        $useSpellcheckSearch = isset(
            $params['solr_params']['spellcheck']
        ) && $params['solr_params']['spellcheck'] == 'true';


        if ($useSpellcheckSearch) {
            if (isset(
                $params['solr_params']['spellcheck.count']
            ) && (int)$params['solr_params']['spellcheck.count'] > 0
            ) {
                $spellcheckCount = (int)$params['solr_params']['spellcheck.count'];
            } else {
                $spellcheckCount = self::DEFAULT_SPELLCHECK_COUNT;
            }

            $_params['solr_params'] += array(
                'spellcheck.collate' => 'true',
                'spellcheck.dictionary' => 'magento_spell' . $languageSuffix,
                'spellcheck.extendedResults' => 'true',
                'spellcheck.count' => $spellcheckCount
            );
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
                } else {
                    $solrQuery->addParam($name, $value);
                }
            }
        }

        $filtersConditions = $this->_prepareFilters($_params['filters']);
        foreach ($filtersConditions as $condition) {
            $solrQuery->addFilterQuery($condition);
        }

        $this->_client->setServlet(SolrClient::SEARCH_SERVLET_TYPE, 'select');
        /**
         * Store filtering
         */
        if ($_params['store_id'] > 0) {
            $solrQuery->addFilterQuery('store_id:' . $_params['store_id']);
        }

        try {
            $this->ping();
            $response = $this->_client->query($solrQuery);
            $data = $response->getResponse();

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
                    if (isset(
                        $params['spellcheck_result_counts']
                    ) && $params['spellcheck_result_counts'] == true && count(
                        $resultSuggestions
                    ) && $spellcheckCount > 0
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
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
    }

    /**
     * Checks if Solr server is still up
     *
     * @return bool
     */
    public function ping()
    {
        $this->_clientHelper->getSolrSupportedLanguages();
        return parent::ping();
    }

    /**
     * Retrieve attribute solr field name
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute|string $attribute
     * @param string $target - default|sort|nav
     * @return string|bool
     */
    public function getSearchEngineFieldName($attribute, $target = 'default')
    {
        return parent::getSearchEngineFieldName($attribute, $target);
    }
}
