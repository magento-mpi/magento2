<?php
/**
 * Layer price filter
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Abstract 
{
    const MIN_RANGE_POWER = 10;
    
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'price';
    }
    
    /**
     * Apply price filter to collection
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) 
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        if ($filter) {
            $range = $this->getPriceRange($filter);
            $this->getLayer()->getProductCollection()
                ->addFieldToFilter('price', array(
                    'from'  => ($filter-1)*$range,
                    'to'    => $filter*$range,
                ));
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderItemLabel($range, $filter), $filter)
            );
            $this->_items = array();
        }
        return $this;
    }
    
    public function getName()
    {
        return __('Price');
    }

    /**
     * Retrieve price range for build filter
     *
     * @return int
     */
    public function getPriceRange($filterValue=null)
    {
        $range = $this->getData('price_range');
        if (is_null($range)) {
            $maxPrice = $this->getMaxPriceInt();
            $index = 1;
            do {
                $range = pow(10, (strlen(floor($maxPrice))-$index));
                $items = $this->getRangeItemCounts($range);
                $index++;
            }
            while($range>self::MIN_RANGE_POWER && count($items)<2);
            
            $this->setData('price_range', $range);
        }
        return $range;
    }
    
    public function getMaxPriceInt()
    {
        $maxPrice = $this->getData('max_price_int');
        if (is_null($maxPrice)) {
            $maxPrice = Mage::getSingleton('catalog/layer')->getProductCollection()
                ->getMaxAttributeValue('price');
            $maxPrice = floor($maxPrice);
            $this->setData('max_price_int', $maxPrice);
        }
        return $maxPrice;
    }
    
    public function getRangeItemCounts($range)
    {
        $items = $this->getData('range_item_counts_'.$range);
        if (is_null($items)) {
            $items = Mage::getSingleton('catalog/layer')->getProductCollection()
                ->getAttributeValueCountByRange('price', $range);
            $this->setData('range_item_counts_'.$range, $items);
        }
        return $items;
    }
    
    /**
     * Retrieve filter items
     *
     * @return array
     */
    protected function _initItems()
    {
        $range      = $this->getPriceRange();
        $dbRanges   = $this->getRangeItemCounts($range);
        $items = array();
        
        foreach ($dbRanges as $index=>$count) {
        	$items[] = $this->_createItem($this->_renderItemLabel($range, $index), $index, $count);
        }
        
        $this->_items = $items;
        return $this;
    }
    
    protected function _renderItemLabel($range, $value)
    {
        $store = Mage::getSingleton('core/store');
        return $store->formatPrice(($value-1)*$range).' - '.$store->formatPrice($value*$range);
    }
}
