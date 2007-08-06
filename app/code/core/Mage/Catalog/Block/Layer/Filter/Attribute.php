<?php
/**
 * Catalog attribute layer filter
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter
{
    protected function _initFilter()
    {
        $this->_filter = Mage::getModel('catalog/layer_filter_attribute')
            ->setAttributeModel($this->getAttributeModel())
            ->apply($this->getRequest());
        return $this;
    }
    
    public function getName()
    {
        return $this->getAttributeModel()->getFrontend()->getLabel();
    }
    
    public function getItems()
    {
        return $this->_filter->getItems();
    }
}
