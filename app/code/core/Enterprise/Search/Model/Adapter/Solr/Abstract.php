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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Solr search engine abstract adapter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Search_Model_Adapter_Solr_Abstract extends Enterprise_Search_Model_Adapter_Abstract
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
        return Mage::helper('enterprise_search')->getLanguageCodeByLocaleCode($localeCode);
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
        return Mage::helper('enterprise_search')->getLanguageSuffix($localeCode);
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
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
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
     * Prepare index data for using in Solr metadata.
     * Add language code suffix to text fields and type suffix for not text dynamic fields.
     * Prepare sorting fields.
     *
     * @param array $data
     * @param array $attributesParams
     * @param string|null $localeCode
     *
     * @return array
     */
    protected function _prepareIndexData($data, $attributesParams, $localeCode = null)
    {
        if (!is_array($data) || empty($data)) {
            return array();
        }

        $fieldPrefix    = $this->_advancedIndexFieldsPrefix;
        $fieldPrefixLen = strlen($fieldPrefix);
        $languageSuffix = $this->_getLanguageSuffix($localeCode);

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $attributesParams)) {
                $backendType    = $attributesParams[$key]['backendType'];
                $frontendInput  = $attributesParams[$key]['frontendInput'];
                $usedForSortBy  = $attributesParams[$key]['usedForSortBy'];
            } else {
                $backendType    = null;
                $frontendInput  = null;
                $usedForSortBy  = false;
            }

            if (!$usedForSortBy && in_array($key, $this->_usedFields)) {
                continue;
            }

            if ($frontendInput == 'multiselect') {
                $preparedValue = array();
                foreach ($value as $val) {
                    $preparedValue = array_merge($preparedValue, explode($this->_separator, $val));
                }
                $preparedValue = array_unique($preparedValue);

                $fieldType = 'multi';
            } elseif ($frontendInput == 'select' || $frontendInput == 'boolean') {
                if (is_array($value)) {
                    $preparedValue = array_unique($value);
                }

                $fieldType = 'select';
            } elseif (in_array($backendType, $this->_textFieldTypes) || substr($key, 0, 8) == 'fulltext') {
                if (is_array($value)) {
                    $preparedValue = implode(' ', array_unique($value));
                } else {
                    $preparedValue = $value;
                }

                $fieldType = 'text';
            } elseif ($backendType != 'static') {
                if ($backendType == 'datetime') {
                    if (is_array($value)) {
                        $preparedValue = array();
                        foreach ($value as &$val) {
                            $val = $this->_getSolrDate($data['store_id'], $val);
                            if (!empty($val)) {
                                $preparedValue[] = $val;
                            }
                        }

                        $preparedValue = array_unique($preparedValue);
                    } else {
                        $preparedValue = $this->_getSolrDate($data['store_id'], $value);
                    }
                } else {
                    $preparedValue = $value;
                }

                $fieldType = $backendType;
            }

            if ($usedForSortBy) {
                if (is_array($value)) {
                    if (array_key_exists($data['id'], $value)) {
                        $sortValue = $value[$data['id']];
                    } else {
                        $sortValue = null;
                    }
                } else {
                    $sortValue = $value;
                }

                if (strlen($sortValue)) {
                    if ($fieldType == 'text') {
                        $sorFieldName = 'attr_sort_' . $key . $languageSuffix;
                    } else {
                        $sorFieldName = 'attr_sort_' . $fieldType . '_' . $key;
                    }

                    $data[$sorFieldName] = $sortValue;
                }

                if ($attributesParams[$key]['usedForSortOnly']) {
                    unset($data[$key]);
                    continue;
                }
            }

            if (!in_array($key, $this->_usedFields)
                && (!empty($preparedValue)
                    || (!is_array($preparedValue) && strlen($preparedValue))
                )
            ) {
                if (substr($key, 0, $fieldPrefixLen) == $fieldPrefix) {
                    $fieldName = substr($key, $fieldPrefixLen);
                } elseif ($fieldType == 'text') {
                    $fieldName = $key . $languageSuffix;
                } else {
                    $fieldName = 'attr_' . $fieldType . '_' . $key;
                }

                $data[$fieldName] = $preparedValue;
            }
            unset($data[$key]);
        }

        return $data;
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

        $languageSuffix = $this->_getLanguageSuffix(
            Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE)
        );

        /**
         * Support specifying sort by field as only string name of field
         */
        if (!empty($sortBy) && !is_array($sortBy)) {
            if ($sortBy == 'relevance') {
                $sortBy = 'score';
            } elseif ($sortBy == 'name') {
                $sortBy = 'alphaNameSort' . $languageSuffix;
            } elseif ($sortBy == 'position') {
                $sortBy = 'position_category_' . Mage::registry('current_category')->getId();
            } elseif ($sortBy == 'price') {
                $websiteId       = Mage::app()->getStore()->getWebsiteId();
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

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
            } elseif ($sortField == 'name') {
                $sortField = 'alphaNameSort' . $languageSuffix;
            } elseif ($sortField == 'position') {
                $sortField = 'position_category_' . Mage::registry('current_category')->getId();
            } elseif ($sortField == 'price') {
                $websiteId       = Mage::app()->getStore()->getWebsiteId();
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

                $sortField = 'price_'. $customerGroupId .'_'. $websiteId;
            } else {
                $sortField = $this->getAttributeSolrFieldName($sortField);
            }

            $result[] = array('sortField' => $sortField, 'sortType' => trim(strtolower($sortType)));
        }

        return $result;
    }

    /**
     * Retrive Solr server status
     *
     * @return float Actual time taken to ping the server, FALSE if timeout or HTTP error status occurs
     */
    public function ping()
    {
        if (is_null($this->_ping)){
            try {
                $this->_ping = $this->_client->ping();
            } catch (Exception $e) {
                $this->_ping = false;
            }
        }

        return $this->_ping;
    }

    /**
     * Retrieve attribute field's name for sorting
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeSolrFieldName($attributeCode)
    {
        if ($attributeCode == 'score') {
            return $attributeCode;
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_product');
        $attribute  = Mage::getSingleton('eav/config')->getAttribute($entityType, $attributeCode);

        return Mage::helper('enterprise_search')->getSolrFieldName($attribute, true);
    }
}
