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
class Mage_Catalog_Model_Layer_Filter_Price extends Varien_Object 
{
    public function __construct()
    {
        parent::__construct();
        $this->_apply();
    }
    
    /**
     * Apply price filter to collection
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    protected function _apply()
    {
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
            $this->setData('price_range');
        }
        return $range;
    }
    
    /**
     * Retrieve filter items
     *
     * @return array
     */
    public function getItems()
    {
        $range = $this->getPriceRange();
        $dbRanges = Mage::getSingleton('catalog/layer')->getProductCollection()
            ->getAttributeValueCountByRange('price', $range);
        
        $items = array();
        $store = Mage::getSingleton('core/store');
        for ($i=1;$i<=10;$i++) {
            if (isset($dbRanges[$i])) {
                $items[] = Mage::getModel('catalog/layer_filter_item')
                    ->setLabel($store->formatPrice(($i-1)*$range).' - '.$store->formatPrice($i*$range))
                    ->setValue($i)
                    ->setCount($dbRanges[$i]);
            }
        }
        
        return $items;
    }
}
