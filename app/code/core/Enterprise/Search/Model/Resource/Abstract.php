<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Advanced Catalog Search resource model
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Resource_Abstract extends Mage_Core_Model_Resource_Abstract
{
    /**
     * Defines text type fields
     * Integer attributes are saved at metadata as text because in fact they are values for
     * options of select type inputs but their values are presented as text aliases
     *
     * @var array
     */
    protected $_textFieldTypes = array(
        'text',
        'varchar',
        'int'
    );

    protected function _construct()
    {

    }

    /**
     * Retrieve language code by specified locale code if this locale is supported
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
     * Retrieve filter array
     *
     * @param Enterprise_Search_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @return array
     */
    protected function _getSearchParam($collection, $attribute, $value)
    {
        if (empty($value) ||
            (isset($value['from']) && empty($value['from']) &&
            isset($value['to']) && empty($value['to']))) {
            return false;
        }

        $languageCode = $this->_getLanguageCodeByLocaleCode(
            Mage::app()->getStore()
            ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        $field = $attribute->getAttributeCode();
        $fieldType = $attribute->getBackendType();
        $frontendInput = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_'. $field;
        }
        elseif ($backendType == 'int') {
            $field = 'attr_select_'. $field;
        }
        elseif ($fieldType == 'decimal') {
            $field = 'attr_decimal_'. $field;
        }
        elseif ($fieldType == 'datetime') {
            $field = 'attr_datetime_'. $field;
            if (is_array($value)) {
                foreach ($value as &$val) {
                    if (!is_empty_date($val)) {
                        $date = new Zend_Date(
                            $val,
                            Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                        );
                        $val = $date->toString(Zend_Date::ISO_8601) . 'Z';
                    }
                }
            }
            else {
                if (!is_empty_date($value)) {
                    $date = new Zend_Date(
                        $value,
                        Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                    );
                    $value = $date->toString(Zend_Date::ISO_8601) . 'Z';
                }
            }
        }
        elseif (in_array($fieldType, $this->_textFieldTypes)) {
            $field .= $languageSuffix;
        }

        if ($attribute->usesSource()) {
            $attribute->setStoreId(
                Mage::app()->getStore()->getId()
            );

            foreach ($value as &$val) {
                $val = $attribute->getSource()->getOptionText($val);
            }
        }

        return array($field => $value);
    }

    /**
     * Add filter by attribute rated price
     *
     * @param Enterprise_Search_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param int $rate
     *
     * @return bool
     */
    public function addRatedPriceFilter($collection, $attribute, $value, $rate = 1)
    {
        $collection->addPriceData();
        $collection->addSearchParam(array('price' => $value));

        return true;
    }

    /**
     * Add not indexable field to search
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param Enterprise_Search_Model_Resource_Collection $collection
     *
     * @return bool
     */
    public function prepareCondition($attribute, $value, $collection)
    {
        return $this->addIndexableAttributeFilter($collection, $attribute, $value);
    }

    /**
     * Stub method for compatibility with existing abstract resource model
     *
     * @return null
     */
    public function _getReadAdapter()
    {
        return null;
    }

    /**
     * Stub method for compatibility with existing abstract resource model
     *
     * @return null
     */
    public function _getWriteAdapter()
    {
        return null;
    }
}
