<?php
/**
 * Layer category filter
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
    
    protected function _apply()
    {
        
    }
    
    public function getItems()
    {
        $categoty = Mage::getSingleton('catalog/layer')->getCurrentCategory();
        
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
