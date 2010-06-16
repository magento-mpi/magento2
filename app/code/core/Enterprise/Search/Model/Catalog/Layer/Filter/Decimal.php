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
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Layer Decimal Attribute Filter Model
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Decimal
{
/**
     * Get information about products count in range
     *
     * @param   int $range
     * @return  int
     */
    public function getRangeItemCounts($range)
    {
        $attributeCode = $this->getAttributeModel()->getAttributeCode();
        $rangeKey = $attributeCode . '_item_counts_' . $range;
        $items = $this->getData($rangeKey);
        if (is_null($items)) {
            $field = 'attr_decimal_'. $attributeCode;

            $productCollection = $this->getLayer()->getProductCollection();
            $facets = $productCollection->getFacetedData($field);

            $res = array();
            if (!empty($facets)) {
                foreach ($facets as $key => $count) {
                    preg_match('/TO ([\d\.]+)\]$/', $key, $rangeKey);
                    $rangeKey = $rangeKey[1] / $range;
                    if ($count > 0) {
                        $res[round($rangeKey)] = $count;
                    }
                }
            }
            $items = $res;

            $this->setData($rangeKey, $items);
        }

        return $items;
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
        $range      = $this->getRange();
        $maxValue    = $this->getMaxValue();
        $facets = array();
        $facetCount  = ceil($maxValue / $range);

        for ($i = 0; $i < $facetCount; $i++) {
            $facets[] = array(
                'from' => $i * $range,
                'to'   => ($i + 1) * $range - 0.001
            );
        }

        $attributeCode = $this->getAttributeModel()->getAttributeCode();
        $field      = 'attr_decimal_' . $attributeCode;

        $productCollection = $this->getLayer()->getProductCollection();
        $productCollection->setFacetCondition($field, $facets);

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
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param int $range
     * @param int $index    the range factor
     *
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Decimal
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
