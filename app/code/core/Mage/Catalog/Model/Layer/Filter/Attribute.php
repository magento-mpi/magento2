<?php
/**
 * Layer attribute filter
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Abstract 
{
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'attribute';
    }
    
    public function apply(Zend_Controller_Request_Abstract $request) 
    {
        return $this;
    }
    
    protected function _initItems()
    {
        $attribute = $this->getAttributeModel();
        $options = $attribute->getFrontend()->getSelectOptions();
        $this->_requestVar = $attribute->getAttributeCode();
        
        $items=array();
        foreach ($options as $option) {
            if (strlen($option['value'])) {
                $items[] = Mage::getModel('catalog/layer_filter_item')
                    ->setFilter($this)
                    ->setLabel($option['label'])
                    ->setValue($option['value'])
                    ->setCount(1);
            }
        }
        
        $this->_items = $items;
        return $this;
    }
}
