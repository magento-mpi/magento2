<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Resource;

use Magento\Catalog\Model\Resource\Eav\Attribute;

/**
 * Advanced Catalog Search resource model
 */
class Advanced extends \Magento\Framework\Model\Resource\AbstractResource
{
    /**
     * Defines text type fields
     * Integer attributes are saved at metadata as text because in fact they are values for
     * options of select type inputs but their values are presented as text aliases
     *
     * @var array
     */
    protected $_textFieldTypes = array('text', 'varchar', 'int');

    /**
     * @var \Magento\Search\Model\Resource\Solr\Engine
     */
    protected $_resourceEngine;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Search\Model\Resource\Solr\Engine $resourceEngine
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        Solr\Engine $resourceEngine,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        parent::__construct();
        $this->_resourceEngine = $resourceEngine;
        $this->_localeDate = $localeDate;
        $this->dateTime = $dateTime;
    }

    /**
     * Empty construct
     *
     * @return void
     */
    protected function _construct()
    {
    }

    /**
     * Add filter by indexable attribute
     *
     * @param \Magento\Search\Model\Resource\Collection $collection
     * @param Attribute $attribute
     * @param string|array $value
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
     * @param Attribute $attribute
     * @param string|array $value
     * @return array
     */
    protected function _getSearchParam($collection, $attribute, $value)
    {
        if (!is_string(
            $value
        ) && empty($value) || is_string(
            $value
        ) && strlen(
            trim($value)
        ) == 0 || is_array(
            $value
        ) && isset(
            $value['from']
        ) && empty($value['from']) && isset(
            $value['to']
        ) && empty($value['to'])
        ) {
            return array();
        }

        if (!is_array($value)) {
            $value = array($value);
        }

        $field = $this->_resourceEngine->getSearchEngineFieldName($attribute, 'nav');

        if ($attribute->getBackendType() == 'datetime') {
            $format = $this->_localeDate->getDateFormat(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);
            foreach ($value as &$val) {
                if (!$this->dateTime->isEmptyDate($val)) {
                    $date = new \Magento\Framework\Stdlib\DateTime\Date($val, $format);
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
     * @param Attribute $attribute
     * @param string|array $value
     * @param int $rate
     * @return true
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
     * @param Attribute $attribute
     * @param string|array $value
     * @param \Magento\Search\Model\Resource\Collection $collection
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
