<?php
/**
 * Wishlist shared items controllers
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_SharedController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() 
	{
		
		$wishlist = Mage::getModel('wishlist/wishlist')
			->loadByCode($this->getRequest()->getParam('code'));
		if(!$wishlist->getId()) {
			$this->norouteAction();
		} else {
			Mage::register('shared_wishlist', $wishlist);
			$this->loadLayout();
			$this->_initLayoutMessages('wishlist/session');
			$this->getLayout()->getBlock('content')
				->append(
					$this->getLayout()->createBlock('wishlist/share_wishlist','customer.wishlist')
			);
			$this->renderLayout();
		}
		
	}
	
	public function allcartAction() 
	{
		$wishlist = Mage::getModel('wishlist/wishlist')
			->loadByCode($this->getRequest()->getParam('code'));
		if(!$wishlist->getId()) {
			$this->norouteAction();
		} else {
			$quote = Mage::getSingleton('checkout/session')->getQuote();
		
			$wishlist->getItemCollection()->load();
			foreach ($wishlist->getItemCollection() as $item) {
	 			 try {        
		            $product = Mage::getModel('catalog/product')->load($item->getProductId())->setQty(1);
		            $quote->addProduct($product)->save();
	            	$item->delete();
	            }
				catch(Exception $e) {
					//
				}
			}
			
			$this->_redirect('checkout/cart');
		}
		
	}
}// Class Mage_Wishlist_SharedController END