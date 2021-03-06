<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Resource;

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
    protected $_textFieldTypes = ['text', 'varchar', 'int'];

    /**
     * @var \Magento\Solr\Model\Resource\Solr\Engine
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
     * @param \Magento\Solr\Model\Resource\Solr\Engine $resourceEngine
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
     * @param \Magento\Solr\Model\Resource\Collection $collection
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
     * @param \Magento\Solr\Model\Resource\Collection $collection
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
            return [];
        }

        if (!is_array($value)) {
            $value = [$value];
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
            return [];
        } else {
            return [$field => $value];
        }
    }

    /**
     * Add filter by attribute rated price
     *
     * @param \Magento\Solr\Model\Resource\Collection $collection
     * @param Attribute $attribute
     * @param string|array $value
     * @param int $rate
     * @return true
     */
    public function addRatedPriceFilter($collection, $attribute, $value, $rate = 1)
    {
        $collection->addPriceData();
        $fieldName = $this->_resourceEngine->getSearchEngineFieldName($attribute);
        $collection->addSearchParam([$fieldName => $value]);

        return true;
    }

    /**
     * Add not indexable field to search
     *
     * @param Attribute $attribute
     * @param string|array $value
     * @param \Magento\Solr\Model\Resource\Collection $collection
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
