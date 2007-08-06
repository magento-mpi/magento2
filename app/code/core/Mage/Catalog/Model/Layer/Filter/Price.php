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
    public function apply(Zend_Controller_Request_Abstract $request) 
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        if ($filter) {
            $range = $this->getPriceRange();
            Mage::getSingleton('catalog/layer')->getProductCollection()
                ->addFieldToFilter('price', array(
                    'from'  => ($filter-1)*$range,
                    'to'    => $filter*$range,
                ));
        }
        return $this;
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
        $store = Mage::getSingleton('core/store');
        for ($i=1;$i<=10;$i++) {
            if (isset($dbRanges[$i])) {
                $items[] = Mage::getModel('catalog/layer_filter_item')
                    ->setFilter($this)
                    ->setLabel($store->formatPrice(($i-1)*$range).' - '.$store->formatPrice($i*$range))
                    ->setValue($i)
                    ->setCount($dbRanges[$i]);
            }
        }
        
        $this->_items = $items;
        return $this;
    }
}
