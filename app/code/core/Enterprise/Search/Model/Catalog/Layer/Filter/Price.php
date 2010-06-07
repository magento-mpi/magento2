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

        return Mage::getResourceSingleton('enterprise_search/facets_price');
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
            $customerGroupId = Mage::getModel('customer/session')->getCustomerGroupId();
            $priceField      = 'price_'. $customerGroupId .'_'. $websiteId;

            $params['facet'] = array(
                'field'  => $priceField,
                'values' => $priceFacets
            );

            $items = $this->_getResource()->getCount($this, $range, $params);
            $this->setData($rangeKey, $items);
        }

        return $items;
    }


}
