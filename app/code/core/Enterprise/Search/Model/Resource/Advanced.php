<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Advanced Catalog Search resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Resource_Advanced extends Mage_Core_Model_Resource_Abstract
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

    /**
     * Empty construct
     */
    protected function _construct()
    {

    }

    /**
     * Add filter by indexable attribute
     *
     * @param Enterprise_Search_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     *
     * @return bool
     */
    public function addIndexableAttributeModifiedFilter($collection, $attribute, $value)
    {
        $param = $this->_getSearchParam($collection, $attribute, $value);

        if (!empty($param)) {
            $collection->addSearchParam($param);
            return true;
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
        if (empty($value)
            || (isset($value['from']) && empty($value['from'])
            && isset($value['to']) && empty($value['to']))
        ) {
            return false;
        }

        $localeCode = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
        $languageSuffix = Mage::helper('Enterprise_Search_Helper_Data')->getLanguageSuffix($localeCode);

        $field = $attribute->getAttributeCode();
        $backendType = $attribute->getBackendType();
        $frontendInput = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_'. $field;
        } elseif ($frontendInput == 'select' || $frontendInput == 'boolean') {
            $field = 'attr_select_'. $field;
        } elseif ($backendType == 'decimal') {
            $field = 'attr_decimal_'. $field;
        } elseif ($backendType == 'datetime') {
            $field = 'attr_datetime_'. $field;
            $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $invalidDateMessage = Mage::helper('Enterprise_Search_Helper_Data')->__('Specified date is invalid.');
            if (is_array($value)) {
                foreach ($value as &$val) {
                    if (!is_empty_date($val)) {
                        if (!Zend_Date::isDate($val, $dateFormat)) {
                            Mage::throwException($invalidDateMessage);
                        }
                        $date = new Zend_Date($val, $dateFormat);
                        $val = $date->toString(Zend_Date::ISO_8601) . 'Z';
                    }
                }
            } else {
                if (!is_empty_date($value)) {
                    if (!Zend_Date::isDate($value, $dateFormat)) {
                        Mage::throwException($invalidDateMessage);
                    }
                    $date = new Zend_Date($value, $dateFormat);
                    $value = array($date->toString(Zend_Date::ISO_8601) . 'Z');
                }
            }
        } elseif (in_array($backendType, $this->_textFieldTypes)) {
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

        if (empty($value)) {
            return array();
        } else {
            return array($field => $value);
        }
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
        return $this->addIndexableAttributeModifiedFilter($collection, $attribute, $value);
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
