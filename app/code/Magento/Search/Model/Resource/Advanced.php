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
namespace Magento\Search\Model\Resource;

class Advanced extends \Magento\Core\Model\Resource\AbstractResource
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
     * @param \Magento\Search\Model\Resource\Collection $collection
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
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
     * @param \Magento\Search\Model\Resource\Collection $collection
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
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

        $field = \Mage::getResourceSingleton('\Magento\Search\Model\Resource\Engine')
                ->getSearchEngineFieldName($attribute, 'nav');

        if ($attribute->getBackendType() == 'datetime') {
            $format = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
            foreach ($value as &$val) {
                if (!is_empty_date($val)) {
                    $date = new \Zend_Date($val, $format);
                    $val = $date->toString(\Zend_Date::ISO_8601) . 'Z';
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
     * @param \Magento\Search\Model\Resource\Collection $collection
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param string|array $value
     * @param int $rate
     *
     * @return bool
     */
    public function addRatedPriceFilter($collection, $attribute, $value, $rate = 1)
    {
        $collection->addPriceData();
        $fieldName = \Mage::getResourceSingleton('\Magento\Search\Model\Resource\Engine')
                ->getSearchEngineFieldName($attribute);
        $collection->addSearchParam(array($fieldName => $value));

        return true;
    }

    /**
     * Add not indexable field to search
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param string|array $value
     * @param \Magento\Search\Model\Resource\Collection $collection
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
