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
 * Layer decimal attribute filter
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Model\Catalog\Layer\Filter;

class Decimal extends \Magento\Catalog\Model\Layer\Filter\Decimal
{
    /**
     * Get data for build decimal filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $range = $this->getRange();
        $attribute_code = $this->getAttributeModel()->getAttributeCode();
        $facets = $this->getLayer()->getProductCollection()->getFacetedData('attr_decimal_' . $attribute_code);

        $data = array();
        if (!empty($facets)) {
            foreach ($facets as $key => $count) {
                preg_match('/TO ([\d\.]+)\]$/', $key, $rangeKey);
                $rangeKey = $rangeKey[1] / $range;
                if ($count > 0) {
                    $rangeKey = round($rangeKey);
                    $data[] = array(
                        'label' => $this->_renderItemLabel($range, $rangeKey),
                        'value' => $rangeKey . ',' . $range,
                        'count' => $count,
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Apply decimal range filter to product collection
     *
     * @param \Zend_Controller_Request_Abstract $request
     * @param \Magento\Catalog\Block\Layer\Filter\Decimal $filterBlock
     * @return \Magento\Catalog\Model\Layer\Filter\Decimal
     */
    public function apply(\Zend_Controller_Request_Abstract $request, $filterBlock)
    {
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

            $this->applyFilterToCollection($this, $range, $index);
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderItemLabel($range, $index), $filter)
            );

            $this->_items = array();
        }

        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return \Magento\Search\Model\Catalog\Layer\Filter\Decimal
     */
    public function addFacetCondition()
    {
        $range    = $this->getRange();
        $maxValue = $this->getMaxValue();
        if ($maxValue > 0) {
            $facets = array();
            $facetCount = ceil($maxValue / $range);
            for ($i = 0; $i < $facetCount; $i++) {
                $facets[] = array(
                    'from' => $i * $range,
                    'to'   => ($i + 1) * $range - 0.001
                );
            }

            $attributeCode = $this->getAttributeModel()->getAttributeCode();
            $field         = 'attr_decimal_' . $attributeCode;

            $this->getLayer()->getProductCollection()->setFacetCondition($field, $facets);
        }

        return $this;
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Price $filter
     * @param int $range
     * @param int $index    the range factor
     *
     * @return \Magento\Search\Model\Catalog\Layer\Filter\Decimal
     */
    public function applyFilterToCollection($filter, $range, $index)
    {
        $productCollection = $filter->getLayer()->getProductCollection();
        $attributeCode     = $filter->getAttributeModel()->getAttributeCode();
        $field             = 'attr_decimal_'. $attributeCode;

        $value = array(
            $field => array(
                'from' => ($range * ($index - 1)),
                'to'   => $range * $index - 0.001
            )
        );

        $productCollection->addFqFilter($value);
        return $this;
    }
}
