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
 * Layer price filter
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    /**
     * Retrieve resource model
     *
     * @return object
     */
    protected function _getResource()
    {
        $engineClassName         = get_class(Mage::helper('catalogsearch')->getEngine());
        $fulltextEngineClassName = get_class(Mage::getResourceSingleton('catalogsearch/fulltext_engine'));

        if ($engineClassName == $fulltextEngineClassName) {
            return parent::_getResource();
        }

        return Mage::getResourceSingleton('enterprise_search/catalog_facets_price');
    }

    /**
     * Get information about products count in range
     *
     * @param   int $range
     * @return  int
     */
    public function getRangeItemCounts($range)
    {
        $rangeKey = 'range_item_counts_' . $range;
        $items = $this->getData($rangeKey);
        if (is_null($items)) {
            $maxPrice    = $this->getMaxPriceInt();
            $priceFacets = array();
            $facetCount  = ceil($maxPrice / $range);

            for ($i = 0; $i < $facetCount; $i++) {
                $priceFacets[] = array(
                    'from' => $i * $range,
                    'to'   => ($i + 1) * $range
                );
            }

            $websiteId       = Mage::app()->getStore()->getWebsiteId();
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $priceField      = 'price_'. $customerGroupId .'_'. $websiteId;

//            $params['facet'] = array(
//                'field'  => $priceField,
//                'values' => $priceFacets
//            );

            //$items = $this->getCount($this, $range, $params);

            //$attribute = $filter->getAttributeModel();
            $productCollection = $this->getLayer()->getProductCollection();
            //$facets = $productCollection->getFacets($params);
            $facets = $productCollection->getFacetedData($priceField);
            //mage::log($facets);die;

            //$paramName = $params['facet']['field'];
            $res = array();
            if (!empty($facets)) {
                foreach ($facets as $key => $count) {
                    preg_match('/TO (\d+)\]$/', $key, $rangeKey);
                    $rangeKey = $rangeKey[1] / $range;
                    if ($count > 0) {
                        $res[$rangeKey] = $count;
                    }
                }
            }
            $items = $res;

            $this->setData($rangeKey, $items);
        }

        return $items;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $range      = $this->getPriceRange();

        $maxPrice    = $this->getMaxPriceInt();
        $priceFacets = array();
        $facetCount  = ceil($maxPrice / $range);

        for ($i = 0; $i < $facetCount; $i++) {
            $priceFacets[] = array(
                'from' => $i * $range,
                'to'   => ($i + 1) * $range
            );
        }

        $websiteId       = Mage::app()->getStore()->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $priceField      = 'price_'. $customerGroupId .'_'. $websiteId;

//        $params['facet'] = array(
//            'field'  => $priceField,
//            'values' => $priceFacets
//        );

        $productCollection = $this->getLayer()->getProductCollection();
        //$productCollection->setFacetCondition($params['facet']);
        $productCollection->setFacetCondition($priceField, $priceFacets);



        /**
         * Filter must be string: $index,$range
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
            $this->setPriceRange((int)$range);

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
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $range, $index)
    {
        $productCollection = $filter->getLayer()->getProductCollection();
        $attribute         = $filter->getAttributeModel();
        $websiteId         = Mage::app()->getStore()->getWebsiteId();
        $customerGroupId   = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $priceField        = 'price_'. $customerGroupId .'_'. $websiteId;

        $value = array(
            $priceField => array(
                'from' => ($range * ($index - 1)),
                'to'   => $range * $index
            )
        );

        $productCollection->addPriceQfFilter($value);

        return $this;
    }

    /**
     * Retrieve array with products counts per price range
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param int $range
     * @param array $params
     *
     * @return array
     */
//    public function getCount($filter, $range, $params = array())
//    {
//        $attribute = $filter->getAttributeModel();
//        $productCollection = $this->getLayer()->getProductCollection();
//        $facets = $productCollection->getFacets($params);
//
//        $paramName = $params['facet']['field'];
//        $res = array();
//        if (isset($facets[$paramName])) {
//            foreach ($facets[$paramName] as $key => $value) {
//                preg_match('/TO (\d+)\]$/', $key, $rangeKey);
//                $rangeKey = $rangeKey[1] / $range;
//                if ($value > 0) {
//                    $res[$rangeKey] = $value;
//                }
//            }
//        }
//
//        return $res;
//    }

        /**
     * Get data for build price filter items
     *
     * @return array
     */
//    protected function _getItemsData()
//    {
//        $range      = $this->getPriceRange();
//        $dbRanges   = $this->getRangeItemCounts($range);
//        $data = array();
//
//        foreach ($dbRanges as $index => $count) {
//            $data[] = array(
//                'label' => $this->_renderItemLabel($range, $index),
//                'value' => $index . ',' . $range,
//                'count' => $count,
//            );
//        }
//
//        //mage::log(magedebugbacktrace(1));die;
//        $productCollection = $this->getLayer()->getProductCollection();
//
//        $websiteId       = Mage::app()->getStore()->getWebsiteId();
//        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
//        $priceField      = 'price_'. $customerGroupId .'_'. $websiteId;
//
//        return $productCollection->getFacetedData($priceField);
//    }
}
