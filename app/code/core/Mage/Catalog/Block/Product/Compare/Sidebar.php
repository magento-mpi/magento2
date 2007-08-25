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
			
			$this->_items->addAttributeToSelect('name')
				->useProductItem()
				->load();
 		}
 		
 		return $this->_items;
 	}
 	
    public function getRemoveUrl($item)
    {
        return $this->getUrl('catalog/product_compare/remove',array('product'=>$item->getId()));
    }
    
    public function getClearUrl()
    {
        return $this->getUrl('catalog/product_compare/clear');
    }
    
 	public function getCompareUrl()
 	{
 	    $itemIds = array();
 	    foreach ($this->getItems() as $item) {
 	    	$itemIds[] = $item->getId();
 	    }
 	    
 		return $this->getUrl('catalog/product_compare', array('items'=>implode(',', $itemIds)));
 	}
 } // Class Mage_Catalog_Block_Compare_Sidebar end