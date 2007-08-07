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
        if ($filter && $filter<=10) {
            $range = $this->getPriceRange();
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
    public function getPriceRange()
    {
        $range = $this->getData('price_range');
        if (is_null($range)) {
            $maxPrice  = Mage::getSingleton('catalog/layer')->getProductCollection()
                ->getMaxAttributeValue('price');
            
            $range = pow(10, (strlen(floor($maxPrice))-1));
            $this->setData('price_range', $range);
        }
        return $range;
    }
    
    /**
     * Retrieve filter items
     *
     * @return array
     */
    protected function _initItems()
    {
        $range = $this->getPriceRange();
        $dbRanges = Mage::getSingleton('catalog/layer')->getProductCollection()
            ->getAttributeValueCountByRange('price', $range);
        
        $items = array();
        for ($i=1;$i<=10;$i++) {
            if (isset($dbRanges[$i])) {
                $items[] = $this->_createItem($this->_renderItemLabel($range, $i), $i, $dbRanges[$i]);
            }
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
