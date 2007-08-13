<?php
/**
 * Catalog products compare block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Block_Product_Compare_List extends Mage_Core_Block_Template 
 {
 	protected $_items = null;
 	protected $_attributes = null;
 	
 	public function getItems()
 	{
 		if(is_null($this->_items)) {
 			$this->_items = Mage::getResourceModel('catalog/product_compare_item_collection')
 				->setStoreId(Mage::getSingleton('core/store')->getId());
 			
 			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				$this->_items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
			} else {
				$this->_items->setVisitorId(Mage::getSingleton('core/session')->getLogVisitorId());
			}
			
			$this->_items
				->loadComaparableAttributes()
				->addAttributeToSelect('name')
				->addAttributeToSelect('price')
				->useProductItem()
				->load();
 		}
 		
 		return $this->_items;
 	}
 	
 	public function getAttributes() 
 	{
 		if(is_null($this->_attributes)) {
 			$this->_setAttributesFromProducts();
 		}
 		
 		return $this->_attributes;
 	}
 	
 	protected function _setAttributesFromProducts()
 	{
 		$this->_attributes = array();
 		foreach($this->getItems() as $item) {
 			foreach ($item->getAttributes() as $attribute) {
 				if($attribute->getIsComparable() && !$this->hasAttribute($attribute->getAttributeCode()) && $item->getData($attribute->getAttributeCode())!==null) {
 					$this->_attributes[] = $attribute;
 				}
 			}
 		}
 		
 		return $this;
 	}
 	
 	public function hasAttribute($code) 
 	{
 		foreach($this->_attributes as $attribute) {
 			if($attribute->getAttributeCode()==$code) {
 				return true;
 			}
 		}
 		
 		return false;
 	}
 	
 	public function getPrintUrl()
 	{
 		return $this->getUrl('*/*/*', array('_current'=>true, 'print'=>1));
 	}
 	
 } // Class Mage_Catalog_Block_Product_Compare_List end