<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Layer Decimal Attribute Filter Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    const MIN_RANGE_POWER = 10;

    /**
     * Resource instance
     *
     * @var Mage_Catalog_Model_Resource_Layer_Filter_Decimal
     */
    protected $_resource;

    /**
     * Initialize filter and define request variable
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'decimal';
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Decimal
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('Mage_Catalog_Model_Resource_Layer_Filter_Decimal');
        }
        return $this->_resource;
    }

    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        parent::apply($request, $filterBlock);

        /**
         * Filter must be string: $index, $range
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $filter = explode(',', $filter);
        if (count($filter) != 2) {
            return $this;
        }

        list($index, $range) = $filter;
        if ((int)$index && (int)$range) {
            $this->setRange((int)$range);

            $this->_getResource()->applyFilterToCollection($this, $range, $index);
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderItemLabel($range, $index), $filter)
            );

            $this->_items = array();
        }

        return $this;
    }

    /**
     * Retrieve price aggreagation data cache key
     *
     * @return string
     */
    protected function _getCacheKey()
    {
        $key = $this->getLayer()->getStateKey()
            . '_ATTR_' . $this->getAttributeModel()->getAttributeCode();
        return $key;
    }

    /**
     * Prepare text of item label
     *
     * @param   int $range
     * @param   float $value
     * @return  string
     */
    protected function _renderItemLabel($range, $value)
    {
        $from   = Mage::app()->getStore()->formatPrice(($value - 1) * $range, false);
        $to     = Mage::app()->getStore()->formatPrice($value * $range, false);
        return Mage::helper('Mage_Catalog_Helper_Data')->__('%1 - %2', $from, $to);
    }

    /**
     * Retrieve maximum value from layer products set
     *
     * @return float
     */
    public function getMaxValue()
    {
        $max = $this->getData('max_value');
        if (is_null($max)) {
            list($min, $max) = $this->_getResource()->getMinMax($this);
            $this->setData('max_value', $max);
            $this->setData('min_value', $min);
        }
        return $max;
    }

    /**
     * Retrieve minimal value from layer products set
     *
     * @return float
     */
    public function getMinValue()
    {
        $min = $this->getData('min_value');
        if (is_null($min)) {
            list($min, $max) = $this->_getResource()->getMinMax($this);
            $this->setData('max_value', $max);
            $this->setData('min_value', $min);
        }
        return $min;
    }

    /**
     * Retrieve range for building filter steps
     *
     * @return int
     */
    public function getRange()
    {
        $range = $this->getData('range');
        if (!$range) {
            $maxValue = $this->getMaxValue();
            $index = 1;
            do {
                $range = pow(10, (strlen(floor($maxValue)) - $index));
                $items = $this->getRangeItemCounts($range);
                $index++;
            }
            while ($range > self::MIN_RANGE_POWER && count($items) < 2);
            $this->setData('range', $range);
        }

        return $range;
    }

    /**
     * Retrieve information about products count in range
     *
     * @param int $range
     * @return int
     */
    public function getRangeItemCounts($range)
    {
        $rangeKey = 'range_item_counts_' . $range;
        $items = $this->getData($rangeKey);
        if (is_null($items)) {
            $items = $this->_getResource()->getCount($this, $range);
            $this->setData($rangeKey, $items);
        }
        return $items;
    }

    /**
     * Retrieve data for build decimal filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $data       = array();
        $range      = $this->getRange();
        $dbRanges   = $this->getRangeItemCounts($range);

        foreach ($dbRanges as $index => $count) {
            $data[] = array(
                'label' => $this->_renderItemLabel($range, $index),
                'value' => $index . ',' . $range,
                'count' => $count,
            );
        }

        return $data;
    }
}
