<?php
/**
 * Product attribute values source
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Source
{
    protected $_attribute;
    
    public function __construct() 
    {
        
    }
    
    public function setAttribute(Mage_Catalog_Model_Product_Attribute $attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }
    
    public function getArrOptions()
    {
        $options = $this->_attribute->getOptions();
        $arr = array();
        foreach ($options as $option) {
            $arr[] = array(
                'value' => $option->getId(),
                'label' => $option->getValue(),
            );
        }
        return $arr;
    }
}