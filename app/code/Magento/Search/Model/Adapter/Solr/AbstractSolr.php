<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Adapter\Solr;

/**
 * Solr search engine abstract adapter
 */
abstract class AbstractSolr extends \Magento\Search\Model\Adapter\AbstractAdapter
{
    /**
     * Default number of rows to select
     */
    const DEFAULT_ROWS_LIMIT        = 9999;

    /**
     * Default suggestions count
     */
    const DEFAULT_SPELLCHECK_COUNT  = 1;

    /**
     * Define ping status
     *
     * @var float | bool
     */
    protected $_ping = null;

    /**
     * Array of \Zend_Date objects per store
     *
     * @var array
     */
    protected $_dateFormats = array();

    /**
     * Advanced index fields prefix
     *
     * @var string
     */
    protected $_advancedIndexFieldsPrefix = '';

    /**
     * Client factory
     *
     * @var \Magento\Search\Model\FactoryInterface
     */
    protected $_clientFactory;

    /**
     * @var \SolrClient|\Magento\Search\Model\Client\Solr
     */
    protected $_client;

    /**
     * Search client helper
     *
     * @var \Magento\Search\Helper\ClientInterface
     */
    protected $_clientHelper;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Search\Model\Catalog\Layer\Filter\Price $filterPrice
     * @param \Magento\Search\Model\Resource\Index $resourceIndex
     * @param \Magento\CatalogSearch\Model\Resource\Fulltext $resourceFulltext
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\Collection $attributeCollection
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Search\Model\Factory\Factory $searchFactory
     * @param \Magento\Search\Helper\ClientInterface $clientHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param array $options
     *
     * @throws \Magento\Core\Exception
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Search\Model\Catalog\Layer\Filter\Price $filterPrice,
        \Magento\Search\Model\Resource\Index $resourceIndex,
        \Magento\CatalogSearch\Model\Resource\Fulltext $resourceFulltext,
        \Magento\Catalog\Model\Resource\Product\Attribute\Collection $attributeCollection,
        \Magento\Logger $logger,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Search\Model\Factory\Factory $searchFactory,
        \Magento\Search\Helper\ClientInterface $clientHelper,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Stdlib\DateTime $dateTime,
        $options = array()
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_clientFactory = $searchFactory->getFactory();
        $this->_coreRegistry = $registry;
        $this->_clientHelper = $clientHelper;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->dateTime = $dateTime;
        parent::__construct(
            $customerSession, $filterPrice, $resourceIndex, $resourceFulltext, $attributeCollection,
            $logger, $storeManager, $cache
        );
        try {
            $this->_connect($options);
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new \Magento\Core\Exception(
                __('We were unable to perform the search because a search engine misconfiguration.')
            );
        }
    }

    /**
     * Connect to Solr Client by specified options that will be merged with default
     *
     * @param  array $options
     * @throws \RuntimeException
     * @return SolrClient|\Magento\Search\Model\Client\Solr
     */
    protected function _connect($options = array())
    {
        try {
            $this->_client = $this->_clientFactory->createClient($this->_clientHelper->prepareClientOptions($options));
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }

        if (!is_object($this->_client)) {
            throw new \RuntimeException('Solr client is not set.');
        }

        return $this->_client;
    }

    /**
     * Set advanced index fields prefix
     *
     * @param string $prefix
     */
    public function setAdvancedIndexFieldPrefix($prefix)
    {
        $this->_advancedIndexFieldsPrefix = $prefix;
    }

    /**
     * Retrieve language code by specified locale code if this locale is supported by Solr
     *
     * @param string $localeCode
     * @return false|string
     */
    protected function _getLanguageCodeByLocaleCode($localeCode)
    {
        return $this->_clientHelper->getLanguageCodeByLocaleCode($localeCode);
    }

    /**
     * Prepare language suffix for text fields.
     * For not supported languages prefix _def will be returned.
     *
     * @param  string $localeCode
     * @return string
     */
    protected function _getLanguageSuffix($localeCode)
    {
        return $this->_clientHelper->getLanguageSuffix($localeCode);
    }

    /**
     * Retrieve date value in solr format (ISO 8601) with Z
     * Example: 1995-12-31T23:59:59Z
     *
     * @param int $storeId
     * @param string $date
     *
     * @return string|null
     */
    protected function _getSolrDate($storeId, $date = null)
    {
        if (!isset($this->_dateFormats[$storeId])) {
            $timezone = $this->_coreStoreConfig->getConfig(
                \Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE, $storeId
            );
            $locale   = $this->_coreStoreConfig->getConfig(
                \Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE, $storeId
            );
            $locale   = new \Zend_Locale($locale);

            $dateObj  = new \Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dateFormats[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if ($this->dateTime->isEmptyDate($date)) {
            return null;
        }

        list($dateObj, $localeDateFormat) = $this->_dateFormats[$storeId];
        $dateObj->setDate($date, $localeDateFormat);

        return $dateObj->toString(\Zend_Date::ISO_8601) . 'Z';
    }

    /**
     * Prepare search conditions from query
     *
     * @param string|array $query
     *
     * @return string
     */
    protected function prepareSearchConditions($query)
    {
        if (is_array($query)) {
            $searchConditions = array();
            foreach ($query as $field => $value) {
                if (is_array($value)) {
                    if ($field == 'price' || isset($value['from']) || isset($value['to'])) {
                        $from = (isset($value['from']) && strlen(trim($value['from'])))
                            ? $this->_prepareQueryText($value['from'])
                            : '*';
                        $to = (isset($value['to']) && strlen(trim($value['to'])))
                            ? $this->_prepareQueryText($value['to'])
                            : '*';
                        $fieldCondition = "$field:[$from TO $to]";
                    } else {
                        $fieldCondition = array();
                        foreach ($value as $part) {
                            $part = $this->_prepareFilterQueryText($part);
                            $fieldCondition[] = $field .':'. $part;
                        }
                        $fieldCondition = '('. implode(' OR ', $fieldCondition) .')';
                    }
                } else {
                    if ($value != '*') {
                        $value = $this->_prepareQueryText($value);
                    }
                    $fieldCondition = $field .':'. $value;
                }

                $searchConditions[] = $fieldCondition;
            }

            $searchConditions = implode(' AND ', $searchConditions);
        } else {
            $searchConditions = $this->_prepareQueryText($query);
        }

        return $searchConditions;
    }

    /**
     * Prepare facet fields conditions
     *
     * @param array $facetFields
     * @return array
     */
    protected function _prepareFacetConditions($facetFields)
    {
        $result = array();

        if (is_array($facetFields)) {
            $result['facet'] = 'on';
            foreach ($facetFields as $facetField => $facetFieldConditions) {
                if (empty($facetFieldConditions)) {
                    $result['facet.field'][] = $facetField;
                } else {
                    foreach ($facetFieldConditions as $facetCondition) {
                        if (is_array($facetCondition) && isset($facetCondition['from'])
                            && isset($facetCondition['to'])
                        ) {
                            $from = strlen(trim($facetCondition['from']))
                                ? $facetCondition['from']
                                : '*';
                            $to = strlen(trim($facetCondition['to']))
                                ? $facetCondition['to']
                                : '*';
                            $fieldCondition = "$facetField:[$from TO $to]";
                        } else {
                            $facetCondition = $this->_prepareQueryText($facetCondition);
                            $fieldCondition = $this->_prepareFieldCondition($facetField, $facetCondition);
                        }

                        $result['facet.query'][] = $fieldCondition;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Prepare fq filter conditions
     *
     * @param array $filters
     * @return array
     */
    protected function _prepareFilters($filters)
    {
        $result = array();

        if (is_array($filters) && !empty($filters)) {
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    if (isset($value['from']) || isset($value['to'])) {
                        $from = (isset($value['from']) && !empty($value['from']))
                            ? $value['from']
                            : '*';
                        $to = (isset($value['to']) && !empty($value['to']))
                            ? $value['to']
                            : '*';
                        $fieldCondition = "$field:[$from TO $to]";
                    } else {
                        $fieldCondition = array();
                        foreach ($value as $part) {
                            $part = $this->_prepareFilterQueryText($part);
                            $fieldCondition[] = $this->_prepareFieldCondition($field, $part);
                        }
                        $fieldCondition = '(' . implode(' OR ', $fieldCondition) . ')';
                    }
                } else {
                    $value = $this->_prepareFilterQueryText($value);
                    $fieldCondition = $this->_prepareFieldCondition($field, $value);
                }

                $result[] = $fieldCondition;
            }
        }

        return $result;
    }

    /**
     * Prepare sort fields
     *
     * @param array $sortBy
     * @return array
     */
    protected function _prepareSortFields($sortBy)
    {
        $result = array();

        $localeCode = $this->_storeManager->getStore()
            ->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE);
        $languageSuffix = $this->_getLanguageSuffix($localeCode);

        /**
         * Support specifying sort by field as only string name of field
         */
        if (!empty($sortBy) && !is_array($sortBy)) {
            if ($sortBy == 'relevance') {
                $sortBy = 'score';
            } elseif ($sortBy == 'name') {
                $sortBy = 'alphaNameSort' . $languageSuffix;
            } elseif ($sortBy == 'position') {
                $sortBy = 'position_category_' . $this->_coreRegistry->registry('current_category')->getId();
            } elseif ($sortBy == 'price') {
                $websiteId       = $this->_storeManager->getStore()->getWebsiteId();
                $customerGroupId = $this->_customerSession->getCustomerGroupId();

                $sortBy = 'price_'. $customerGroupId .'_'. $websiteId;
            }

            $sortBy = array(array($sortBy => 'asc'));
        }

        foreach ($sortBy as $sort) {
            $_sort = each($sort);
            $sortField = $_sort['key'];
            $sortType = $_sort['value'];
            if ($sortField == 'relevance') {
                $sortField = 'score';
            } elseif ($sortField == 'position') {
                $sortField = 'position_category_' . $this->_coreRegistry->registry('current_category')->getId();
            } elseif ($sortField == 'price') {
                $sortField = $this->getPriceFieldName();
            } else {
                $sortField = $this->getSearchEngineFieldName($sortField, 'sort');
            }

            $result[] = array('sortField' => $sortField, 'sortType' => trim(strtolower($sortType)));
        }

        return $result;
    }

    /**
     * Retrieve Solr server status
     *
     * @return  float|bool Actual time taken to ping the server, FALSE if timeout or HTTP error status occurs
     */
    public function ping()
    {
        if (is_null($this->_ping)) {
            try {
                $this->_ping = $this->_client->ping();
            } catch (\Exception $e) {
                $this->_ping = false;
            }
        }

        return $this->_ping;
    }

    /**
     * Prepare name for system text fields.
     *
     * @param   string $filed
     * @param   string $suffix
     * @param   int  $storeId
     * @return  string
     */
    public function getAdvancedTextFieldName($filed, $suffix = '', $storeId = null)
    {
        $localeCode     = $this->_storeManager->getStore($storeId)
            ->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE);
        $languageSuffix = $this->_clientHelper->getLanguageSuffix($localeCode);

        if ($suffix) {
            $suffix = '_' . $suffix;
        }

        return $filed . $suffix . $languageSuffix;
    }

    /**
     * Retrieve attribute solr field name
     *
     * @param   \Magento\Catalog\Model\Resource\Eav\Attribute|string $attribute
     * @param   string $target - default|sort|nav
     *
     * @return  string|bool
     */
    public function getSearchEngineFieldName($attribute, $target = 'default')
    {
        if (is_string($attribute)) {
            if ($attribute == 'price') {
                return $this->getPriceFieldName();
            }

            $entityType = $this->_eavConfig->getEntityType('catalog_product');
            $attribute  = $this->_eavConfig->getAttribute($entityType, $attribute);
        }

        // Field type defining
        $attributeCode = $attribute->getAttributeCode();
        if ($attributeCode == 'sku') {
            return $target == 'sort' ? 'attr_sort_sku' : 'sku';
        }

        if ($attributeCode == 'price') {
            return $this->getPriceFieldName();
        }

        $backendType    = $attribute->getBackendType();
        $frontendInput  = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $fieldType = 'multi';
        } elseif ($frontendInput == 'select' || $frontendInput == 'boolean') {
            $fieldType = 'select';
        } elseif ($backendType == 'decimal' || $backendType == 'datetime') {
            $fieldType = $backendType;
        } else {
            $fieldType = 'text';
        }

        // Field prefix construction. Depends on field usage purpose - default, sort, navigation
        $fieldPrefix = 'attr_';
        if ($target == 'sort') {
            $fieldPrefix .= $target . '_';
        } elseif ($target == 'nav') {
            if ($attribute->getIsFilterable() || $attribute->getIsFilterableInSearch() || $attribute->usesSource()) {
                $fieldPrefix .= $target . '_';
            }
        }

        if ($fieldType == 'text') {
            $localeCode     = $this->_storeManager->getStore($attribute->getStoreId())
                ->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE);
            $languageSuffix = $this->_clientHelper->getLanguageSuffix($localeCode);
            $fieldName      = $fieldPrefix . $attributeCode . $languageSuffix;
        } else {
            $fieldName      = $fieldPrefix . $fieldType . '_' . $attributeCode;
        }

        return $fieldName;
    }





    // Deprecated methods

    /**
     * Prepare index data for using in Solr metadata.
     * Add language code suffix to text fields and type suffix for not text dynamic fields.
     * Prepare sorting fields.
     *
     * @deprecated after 1.11.2.0
     *
     * @param   array $data
     * @param   array $attributesParams
     * @param   string|null $localeCode
     *
     * @return  array
     */
    protected function _prepareIndexData($data, $attributesParams = array(), $localeCode = null)
    {
        $productId  = $data['id'];
        $storeId    = $data['store_id'];

        if ($productId && $storeId) {
            return $this->_prepareIndexProductData($data, $productId, $storeId);
        }

        return array();
    }

    /**
     * Retrieve attribute field's name for sorting
     *
     * @deprecated after 1.11.2.0
     *
     * @param string $attributeCode
     *
     * @return string
     */
    public function getAttributeSolrFieldName($attributeCode)
    {
        return $this->getSearchEngineFieldName($attributeCode, 'sort');
    }
}
