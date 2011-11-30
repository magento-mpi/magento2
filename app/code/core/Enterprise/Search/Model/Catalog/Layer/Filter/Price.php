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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Layer price filter
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    const CACHE_TAG = 'MAXPRICE';

    /**
     * Return cache tag for layered price filter
     *
     * @return string
     */
    public function getCacheTag()
    {
        return self::CACHE_TAG;
    }

    /**
     * Get facet field name based on current website and customer group
     *
     * @return string
     */
    protected function _getFilterField()
    {
        $websiteId       = Mage::app()->getStore()->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $priceField      = 'price_'. $customerGroupId .'_'. $websiteId;

        return $priceField;
    }

    /**
     * Get data for build price filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $appliedInterval = $this->getInterval();
        if ($appliedInterval) {
            return array();
        }

        $isAuto = (Mage::app()->getStore()
                      ->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_AUTO);
        $facets = $this->getLayer()->getProductCollection()->getFacetedData($this->_getFilterField());
        $data = array();
        if (!empty($facets)) {
            foreach ($facets as $k => $count) {
                if ($count <= 0) {
                    unset($facets[$k]);
                }
            }

            if (!$isAuto && !empty($facets)) {
                $range  = $this->getPriceRange();
                $lastIndex = array_keys($facets);
                $lastIndex = $lastIndex[count($lastIndex) - 1];
            }

            foreach ($facets as $key => $count) {
                preg_match('/\[([\d\.\\\*]+) TO ([\d\.\\\*]+)\]$/', $key, $separator);

                if ($isAuto) {
                    if ($separator[1] == '\\*') {
                        $separator[1] = '';
                    }
                    if ($separator[2] == '\\*') {
                        $separator[2] = '';
                    } else {
                        $separator[2] = round($separator[2], 2);
                    }
                } else {
                    $rangeKey = $separator[2] / $range;

                    $rangeKey = round($rangeKey, 2);
                    $separator[1] = ($rangeKey == 1) ? '' : (($rangeKey - 1) * $range);
                    $separator[2] = ($key == $lastIndex) ? '' : ($rangeKey * $range);
                }

                $data[] = array(
                    'label' => $this->_renderRangeLabel(empty($separator[1]) ? 0 : $separator[1], $separator[2]),
                    'value' => $separator[1] . '-' . $separator[2],
                    'count' => $count
                );
            }
        }

        return $data;
    }

    /**
     * Get maximum price from layer products set using cache
     *
     * @return float
     */
    public function getMaxPriceInt()
    {
        $searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $uniquePart = strtoupper(md5(serialize($searchParams)));
        $cacheKey = 'MAXPRICE_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = Mage::app()->loadCache($cacheKey);
        if (!$cachedData) {
            $stats = $this->getLayer()->getProductCollection()->getStats($this->_getFilterField());

            $max = $stats[$this->_getFilterField()]['max'];
            if (!is_numeric($max)) {
                $max = parent::getMaxPriceInt();
            }

            $cachedData = (float) $max;
            $tags = $this->getLayer()->getStateTags();
            $tags[] = self::CACHE_TAG;
            Mage::app()->saveCache($cachedData, $cacheKey, $tags);
        }

        return $cachedData;
    }

    /**
     * Get data with price separators
     *
     * @return array
     */
    protected function _getSeparators()
    {
        $searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $uniquePart = strtoupper(md5(serialize($searchParams)));
        $cacheKey = 'PRICE_SEPARATORS_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = Mage::app()->loadCache($cacheKey);
        if (!$cachedData) {
            $prices = $this->getLayer()->getProductCollection()->getFieldData($this->_getFilterField());
            /** @var $algorithmModel Mage_Catalog_Model_Layer_Filter_Price_Algorithm */
            $algorithmModel = Mage::getSingleton('catalog/layer_filter_price_algorithm');
            $algorithmModel->setPrices($prices);

            $cachedData = array();
            foreach ($algorithmModel->calculateSeparators() as $separator) {
                $cachedData[] = $separator['from'] . '-' . $separator['to'];
            }
            $cachedData = implode(',', $cachedData);

            $tags = $this->getLayer()->getStateTags();
            $tags[] = self::CACHE_TAG;
            Mage::app()->saveCache($cachedData, $cacheKey, $tags);
        }

        if (!$cachedData) {
            return array();
        }

        $cachedData = explode(',', $cachedData);
        foreach ($cachedData as $k => $v) {
            $cachedData[$k] = explode('-', $v);
        }

        return $cachedData;
    }

    /**
     * Prepare price range to be added to facet conditions
     *
     * @param string|float $from
     * @param string|float $to
     * @return array
     */
    protected function _prepareFacetRange($from, $to)
    {
        if (empty($from)) {
            $from = '*';
        }
        if (empty($to)) {
            $to = '*';
        } elseif ($to != $from) {
            $to -= .001;
        }
        return array('from' => $from, 'to' => $to);
    }

    /**
     * Add params to faceted search generated by algorithm
     *
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Price
     */
    protected function _addCalculatedFacetCondition()
    {
        $priceFacets = array();
        foreach ($this->_getSeparators() as $separator) {
            $priceFacets[] = $this->_prepareFacetRange($separator[0], $separator[1]);
        }
        $this->getLayer()->getProductCollection()->setFacetCondition($this->_getFilterField(), $priceFacets);
    }

    /**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Price
     */
    public function addFacetCondition()
    {
        if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_AUTO) {
            return $this->_addCalculatedFacetCondition();
        }

        $range    = $this->getPriceRange();
        $maxPrice = $this->getMaxPriceInt();
        if ($maxPrice > 0) {
            $priceFacets = array();
            $facetCount  = ceil($maxPrice / $range);

            for ($i = 0; $i < $facetCount + 1; $i++) {
                $to = ($i + 1) * $range;
                if ($i < $facetCount) {
                    $to -= 0.001;
                }
                $priceFacets[] = array(
                    'from' => $i * $range,
                    'to'   => $to
                );
            }

            $this->getLayer()->getProductCollection()->setFacetCondition($this->_getFilterField(), $priceFacets);
        }

        return $this;
    }

    /**
     * Apply filter value to product collection based on filter range and selected value
     *
     * @deprecated since 1.12.0.0
     * @param int $range
     * @param int $index
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    protected function _applyToCollection($range, $index)
    {
        $to = $range * $index;
        if ($to < $this->getMaxPriceInt()) {
            $to -= 0.001;
        }

        $value = array(
            $this->_getFilterField() => array(
                'from' => ($range * ($index - 1)),
                'to'   => $to
            )
        );

        $this->getLayer()->getProductCollection()->addFqFilter($value);

        return $this;
    }

    /**
     * Apply price range filter to collection
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    protected function _applyPriceRange()
    {
        list($from, $to) = $this->getInterval();
        $this->getLayer()->getProductCollection()->addFqFilter(array(
            $this->_getFilterField() => $this->_prepareFacetRange($from, $to)
        ));

        return $this;
    }
}
