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
		$this->loadLayout();
		$this->getLayout()->loadUpdateFile(Mage::getDesign()->getLayoutFilename('catalog/compare.xml'));
		$this->getLayout()->generateBlocks();
		$this->renderLayout();
	}
	
	public function addAction()
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
			if(!$item->getId()) {
				$item->addProductData($product);
				$item->save();
			}
		}
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->getBlock('catalog.compare.sidebar')->toHtml()
		);
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
		
		if($this->getRequest()->getParam('ajax')) {
			$this->getResponse()->setBody('ok');
		} else {
			$this->_redirect('adminhtml/catalog');
		}
	}
	 		
 } // Class Mage_Catalog_CompareController end