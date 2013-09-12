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
 * Solr search engine abstract adapter
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Search_Model_Adapter_Solr_Abstract extends Magento_Search_Model_Adapter_Abstract
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
     * Array of Zend_Date objects per store
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
     * Return client factory
     *
     * @var Magento_Search_Model_Client_FactoryInterface
     */
    protected $_clientFactory;

    /**
     * Logger
     *
     * @var Magento_Core_Model_Logger
     */
    protected $_log;

    /**
     * Search client helper
     *
     * @var Magento_Search_Helper_ClientInterface
     */
    protected $_clientHelper;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Initialize connect to Solr Client
     *
     *
     *
     * @param Magento_Search_Model_Client_FactoryInterface $clientFactory
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Search_Helper_ClientInterface $clientHelper
     * @param Magento_Core_Model_Registry $registry
     * @param array $options
     */
    public function __construct(
        Magento_Search_Model_Client_FactoryInterface $clientFactory,
        Magento_Core_Model_Logger $logger,
        Magento_Search_Helper_ClientInterface $clientHelper,
        Magento_Core_Model_Registry $registry,
        $options = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_clientHelper = $clientHelper;
        $this->_log = $logger;
        $this->_clientFactory = $clientFactory;
        try {
            $this->_connect($options);
        } catch (Exception $e) {
            $this->_log->logException($e);
            Mage::throwException(
                __('We were unable to perform the search because a search engine misconfiguration.')
            );
        }
    }

    /**
     * Connect to Solr Client by specified options that will be merged with default
     *
     * @param  array $options
     * @throws RuntimeException
     * @return SolrClient|Magento_Search_Model_Client_Solr
     */
    protected function _connect($options = array())
    {
        try {
            $this->_client = $this->_clientFactory->createClient($this->_clientHelper->prepareClientOptions($options));
        } catch (Exception $e) {
            $this->_log->logException($e);
        }

        if (!is_object($this->_client)) {
            throw new RuntimeException('Solr client is not set.');
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
            $timezone = Mage::getStoreConfig(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj  = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dateFormats[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if (is_empty_date($date)) {
            return null;
        }

        list($dateObj, $localeDateFormat) = $this->_dateFormats[$storeId];
        $dateObj->setDate($date, $localeDateFormat);

        return $dateObj->toString(Zend_Date::ISO_8601) . 'Z';
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
                                && isset($facetCondition['to'])) {
                            $from = (isset($facetCondition['from']) && strlen(trim($facetCondition['from'])))
                                ? $this->_prepareQueryText($facetCondition['from'])
                                : '*';
                            $to = (isset($facetCondition['to']) && strlen(trim($facetCondition['to'])))
                                ? $this->_prepareQueryText($facetCondition['to'])
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
                    if ($field == 'price' || isset($value['from']) || isset($value['to'])) {
                        $from = (isset($value['from']) && !empty($value['from']))
                            ? $this->_prepareFilterQueryText($value['from'])
                            : '*';
                        $to = (isset($value['to']) && !empty($value['to']))
                            ? $this->_prepareFilterQueryText($value['to'])
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

        $localeCode = Mage::app()->getStore()->getConfig(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_LOCALE);
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
                $websiteId       = Mage::app()->getStore()->getWebsiteId();
                $customerGroupId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();

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
            } catch (Exception $e) {
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
     * @return  string
     */
    public function getAdvancedTextFieldName($filed, $suffix = '', $storeId = null)
    {
        $localeCode     = Mage::app()->getStore($storeId)->getConfig(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_LOCALE);
        $languageSuffix = $this->_clientHelper->getLanguageSuffix($localeCode);

        if ($suffix) {
            $suffix = '_' . $suffix;
        }

        return $filed . $suffix . $languageSuffix;
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
        if (is_string($attribute)) {
            if ($attribute == 'price') {
                return $this->getPriceFieldName();
            }

            $eavConfig  = Mage::getSingleton('Magento_Eav_Model_Config');
            $entityType = $eavConfig->getEntityType('catalog_product');
            $attribute  = $eavConfig->getAttribute($entityType, $attribute);
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
            $localeCode     = Mage::app()->getStore($attribute->getStoreId())
                ->getConfig(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_LOCALE);
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
