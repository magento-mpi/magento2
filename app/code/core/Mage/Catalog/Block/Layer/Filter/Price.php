<?php
/**
 * Catalog layer price filter
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter
{
    protected function _initFilter()
    {
        $this->_filter = Mage::getModel('catalog/layer_filter_price')
            ->apply($this->getRequest());
        return $this;
    }
    
    public function getName()
    {
        return __('Price');
    }
    
    public function getItems()
    {
        return $this->_filter->getItems();
    }
}
