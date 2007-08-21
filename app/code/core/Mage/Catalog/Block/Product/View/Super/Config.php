<?php
/**
 * Catalog super product configurable part block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Block_Product_View_Super_Config extends Mage_Core_Block_Template 
 {
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/view/super/config.phtml');
    }
    
 	public function getAttributes()
 	{
 		if($this->getRequest()->getParam('super_attribute') && is_array($this->getRequest()->getParam('super_attribute'))) {
 			foreach ($this->getRequest()->getParam('super_attribute') as $attributeId=>$attributeValue) {
 				if(!empty($attributeValue) && $attribute = Mage::registry('product')->getResource()->getAttribute($attributeId)) {
 					Mage::registry('product')->getSuperLinkCollection()
 						->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);
 				}
 			}
 		}
 		
 		return Mage::registry('product')->getSuperAttributes(false, true);
 	}
 	
 	public function canDisplayContainer()
 	{
 		return !(bool)$this->getRequest()->getParam('ajax', false);
 	}
 	
 	public function getPricingValue($value)
    {
    	$value = Mage::registry('product')->getPricingValue($value);
    	$numberSign = $value >= 0 ? '+' : '-';
    	return ' ' . $numberSign . ' ' . Mage::getSingleton('core/store')->formatPrice(abs($value));
    }
    
    public function isSelectedOption($value, $attribute) 
    {
    	$selected = $this->getRequest()->getParam('super_attribute', array());
    	if(is_array($selected) && isset($selected[$attribute['attribute_id']]) && $selected[$attribute['attribute_id']]==$value['value_index']) {
    		return true;
    	}
    	
    	return false;
    }
    
    public function getUpdateUrl()
    {
    	return $this->getUrl('*/*/superConfig', array('_current'=>true));    	
    }
    
    public function getUpdatePriceUrl()
    {
    	return $this->getUrl('*/*/price', array('_current'=>true));
    }
 } // Class Mage_Catalog_Block_Product_View_Super_Config end