<?php
/**
 * Catalog comapare controller
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Product_CompareController extends Mage_Core_Controller_Front_Action 
 {
	public function indexAction()
	{
	    $items = $this->getRequest()->getParam('items');
	    if ($items) {
	        $items = explode(',', $items);
	        $list = Mage::getSingleton('catalog/product_compare_list');
            $list->addProducts($items);
	    }
	    
		$this->loadLayout(array('default', 'catalog_compare'), 'catalog_compare');
		$this->renderLayout();
	}
	
	public function addAction()
	{
		$productId = (int)$this->getRequest()->getParam('product');
								
		$product = Mage::getModel('catalog/product')
			->load($productId);
		
		if($product->getId()) {
		    Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
		}
		
		$this->_redirectToReferer();
	}
	
	public function removeAction()
	{
		$productId = (int)$this->getRequest()->getParam('product');
        
		$product = Mage::getModel('catalog/product')
			->load($productId);
				
		if($product->getId()) {
			$item = Mage::getModel('catalog/product_compare_item');
			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				$item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
			} else {
				$item->addVisitorId(Mage::getSingleton('core/session')->getLogVisitorId());
			}
			
			$item->loadByProduct($product);
			
			if($item->getId()) {
				$item->delete();
			}
		} 
		
        $this->_redirectToReferer();
	}
	
	public function clearAction()
	{
		$items = Mage::getResourceModel('catalog/product_compare_item_collection')
				->setStoreId(Mage::getSingleton('core/store')->getId());
			
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
		} else {
			$items->setVisitorId(Mage::getSingleton('core/session')->getLogVisitorId());
		}
		
		$items->load();
		
		$items->walk('delete');
		
        $this->_redirectToReferer();
	} 		
 } // Class Mage_Catalog_CompareController end