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
 * Solr search engine abstract adapter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Search_Model_Adapter_Solr_Abstract extends Enterprise_Search_Model_Adapter_Abstract
{
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
     *
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

    /**
     * Retrieve date value in solr format (ISO 8601) with Z
     * Example: 1995-12-31T23:59:59Z
     *
     * @param int $storeId
     * @param string $date
     *
     * @return string
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
     * Prepare index data for using in Solr metadata
     * Add language code suffix to text fields
     * and type suffix for not text dynamic fields
     *
     * @see $this->_usedFields, $this->_searchTextFields
     *
     * @param array $data
     * @param array $attributesParams
     * @param string|null $localCode
     *
     * @return array
     */
    protected function _prepareIndexData($data, $attributesParams, $localeCode = null)
    {
        if (!is_array($data) || empty($data)) {
            return array();
        }

        $fieldPrefix = $this->_advancedIndexFieldsPrefix;
        $languageCode = $this->_getLanguageCodeByLocaleCode($localeCode);
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        foreach ($data as $key => $value) {

            if (in_array($key, $this->_usedFields) && !in_array($key, $this->_searchTextFields)) {
                continue;
            }
            elseif ($key == 'options') {
                unset($data[$key]);
                continue;
            }

            if (!array_key_exists($key, $attributesParams)) {
                $backendType = (substr($key, 0, 8) == 'fulltext') ? 'text' : null;
                $frontendInput = null;
            }
            else {
                $backendType = $attributesParams[$key]['backendType'];
                $frontendInput = $attributesParams[$key]['frontendInput'];
            }

            if ($frontendInput == 'multiselect') {
                if (!is_array($value)) {
                    $value = explode(' ', $value);
                }
                $data['attr_multi_'. $key] = $value;
                unset($data[$key]);
            }
            elseif ($backendType == 'int') {
                $data['attr_select_'. $key] = $value;
                unset($data[$key]);
            }
            elseif (in_array($backendType, $this->_textFieldTypes) || in_array($key, $this->_searchTextFields)) {
                /*
                 * for groupped products imploding all possible unique values
                 */
                if (is_array($value)) {
                    $value = implode(' ', array_unique($value));
                }

                $data[$key . $languageSuffix] = $value;
                unset($data[$key]);
            }
            elseif ($backendType != 'static') {
                if (substr($key, 0, strlen($fieldPrefix)) == $fieldPrefix) {
                    $data[substr($key, strlen($fieldPrefix))] = $value;
                    unset($data[$key]);
                    continue;
                }

                if ($backendType == 'datetime') {
                    $value = $this->_getSolrDate($data['store_id'], $value);
                }
                $data['attr_'. $backendType .'_'. $key] = $value;
                unset($data[$key]);
            }
        }

        return $data;
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
            }
            catch (Exception $e){
                $this->_ping = false;
            }
        }

        return $this->_ping;
    }

    /**
     * Retrive attribute field's name for sorting
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     *
     * @return string
     */
    public function getAttributeSolrFieldName($attributeCode)
    {
        if ($attributeCode == 'score') {
            return $attributeCode;
        }
        $entityType = Mage::getSingleton('eav/config')
            ->getEntityType('catalog_product');
        $attribute = Mage::getSingleton('eav/config')->getAttribute($entityType, $attributeCode);

        $field = $attributeCode;
        $backendType = $attribute->getBackendType();
        $frontendInput = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_'. $field;
        }
        elseif ($backendType == 'int') {
            $field = 'attr_select_'. $field;
        }
        elseif ($backendType == 'decimal') {
            $field = 'attr_decimal_'. $field;
        }
        elseif (in_array($backendType, $this->_textFieldTypes)) {
            $languageCode = $this->_getLanguageCodeByLocaleCode(
                Mage::app()->getStore()
                ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
            $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

            $field .= $languageSuffix;
        }

        return $field;
    }
}
