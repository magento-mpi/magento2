<?php
/**
 * Catalog comapare sidebar block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Block_Product_Compare_Sidebar extends Mage_Core_Block_Template 
 {
 	protected $_items = null;
 	
 	protected function _construct()
 	{
 		$this->setId('compare');
 	}
 	
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
				->addAttributeToSelect('name')
				->useProductItem()
				->load();
 		}
 		
 		return $this->_items;
 	}
 	
 	public function getJsObjectName()
 	{
 		return $this->getId()."JsObject";
 	}
 	
 	public function getCanDisplayContainer() {
 		if($this->getRequest()->getParam('ajax')) {
 			return false;
 		} 		
 		return true;
 	}
 	
 	public function getRemoveUrlTemplate()
 	{
 		return $this->getUrl('catalog/product_compare/remove',array('product'=>'#{id}'));
 	}
 	
 	public function getAddUrlTemplate()
 	{
 		return $this->getUrl('catalog/product_compare/add',array('product'=>'#{id}'));
 	}
 	
 	public function getCompareUrl()
 	{
 		return $this->getUrl('catalog/product_compare');
 	}
 } // Class Mage_Catalog_Block_Compare_Sidebar end