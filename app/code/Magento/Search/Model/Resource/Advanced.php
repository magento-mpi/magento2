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
 * Advanced Catalog Search resource model
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Model_Resource_Advanced extends Magento_Core_Model_Resource_Abstract
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
     * @var Magento_Search_Model_Resource_Engine
     */
    protected $_resourceEngine;

    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Construct
     *
     * @param Magento_Search_Model_Resource_Engine $resourceEngine
     * @param Magento_Core_Model_LocaleInterface $locale
     */
    public function __construct(
        Magento_Search_Model_Resource_Engine $resourceEngine,
        Magento_Core_Model_LocaleInterface $locale
    ) {
        parent::__construct();
        $this->_resourceEngine = $resourceEngine;
        $this->_locale = $locale;
    }

    /**
     * Empty construct
     */
    protected function _construct()
    {

    }

    /**
     * Add filter by indexable attribute
     *
     * @param Magento_Search_Model_Resource_Collection $collection
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
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
     * @param Magento_Search_Model_Resource_Collection $collection
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @return array
     */
    protected function _getSearchParam($collection, $attribute, $value)
    {
        if ((!is_string($value) && empty($value))
            || (is_string($value) && strlen(trim($value)) == 0)
            || (is_array($value)
                && isset($value['from'])
                && empty($value['from'])
                && isset($value['to'])
                && empty($value['to']))
        ) {
            return array();
        }

        if (!is_array($value)) {
            $value = array($value);
        }

        $field = $this->_resourceEngine->getSearchEngineFieldName($attribute, 'nav');

        if ($attribute->getBackendType() == 'datetime') {
            $format = $this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
            foreach ($value as &$val) {
                if (!is_empty_date($val)) {
                    $date = new Zend_Date($val, $format);
                    $val = $date->toString(Zend_Date::ISO_8601) . 'Z';
                }
            }
            unset($val);
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
     * @param Magento_Search_Model_Resource_Collection $collection
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param int $rate
     *
     * @return bool
     */
    public function addRatedPriceFilter($collection, $attribute, $value, $rate = 1)
    {
        $collection->addPriceData();
        $fieldName = $this->_resourceEngine->getSearchEngineFieldName($attribute);
        $collection->addSearchParam(array($fieldName => $value));

        return true;
    }

    /**
     * Add not indexable field to search
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param Magento_Search_Model_Resource_Collection $collection
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
