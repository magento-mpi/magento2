<?php
/**
 * Wishlist front controller
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_IndexController extends Mage_Core_Controller_Front_Action 
{
	public function preDispatch()
	{
		parent::preDispatch();
               
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
	}
	
	public function indexAction()
	{
		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		} 
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError('Cannot create wishlist');
		}
		
		Mage::register('wishlist', $wishlist);
		
		
		$this->loadLayout();
		$this->_initLayoutMessages('wishlist/session');
		$this->getLayout()->getBlock('content')
			->append(
				$this->getLayout()->createBlock('wishlist/customer_wishlist','customer.wishlist')
			);
		$this->renderLayout();
	}
	
	public function addAction()
	{
		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		} 
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError('Cannot create wishlist');
		}
		
		try {
			$wishlist->addNewItem($this->getRequest()->getParam('product'));
			Mage::getSingleton('wishlist/session')->addSuccess('Product successfully added to wishlist');
		}
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
		}
		
		$this->_redirect('*');
	}
	
	public function updateAction()
	{		
		$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
						
		if($post = $this->getRequest()->getPost()) {
			foreach ($post['description'] as $itemId => $description) {
				$item = Mage::getModel('wishlist/item')->load($itemId);
				if($item->getWishlistId()!=$wishlist->getId()) {
					continue;
				}
				
				try {
	               	$item->setDescription($description)
	               		->save();
                } 
                catch (Exception $e) { }   
			}
		}
		
		
		$this->_redirect('*');
	}
	
	public function removeAction() {
		$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		$id = (int) $this->getRequest()->getParam('item');
		$item = Mage::getModel('wishlist/item')->load($id);
		
		if($item->getWishlistId()==$wishlist->getId()) {
			try {
				$item->delete();
			} 
			catch(Exception $e) {
				Mage::getSingleton('wishlist/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*');
	}
	
	public function cartAction() {
		$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		$id = (int) $this->getRequest()->getParam('item');
		$item = Mage::getModel('wishlist/item')->load($id);
		
		if($item->getWishlistId()==$wishlist->getId()) {
			 try {        
	            $product = Mage::getModel('catalog/product')->load($item->getProductId())->setQty(1);
	            $quote = Mage::getSingleton('checkout/session')->getQuote();
	            $quote->addProduct($product)->save();
            	$item->delete();
            } 
			catch(Exception $e) {
				Mage::getSingleton('wishlist/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('checkout/cart');
	}
	
	public function allcartAction() {
		$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		
		$wishlist->getItemCollection()->load();
		foreach ($wishlist->getItemCollection() as $item) {
 			 try {        
	            $product = Mage::getModel('catalog/product')->load($item->getProductId())->setQty(1);
	            $quote->addProduct($product)->save();
            	$item->delete();
            }
			catch(Exception $e) {
				Mage::getSingleton('wishlist/session')->addError($e->getMessage());
			}
		}
		
		$this->_redirect('checkout/cart');
	}
}// Class Mage_Wishlist_IndexController END