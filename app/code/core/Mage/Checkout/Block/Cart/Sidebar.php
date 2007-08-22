<?php
/**
 * Wishlist sidebar block
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Checkout_Block_Cart_Sidebar extends Mage_Core_Block_Template
{
	public function getCartItems()
	{
		return $this->getQuote()->getAllItems();
	}
    
	public function getSubtotal()
	{
	    foreach ($this->getQuote()->getTotals() as $total) {
	        if ($total->getCode()=='subtotal') {
	            return Mage::currency($total->getValue());
	        }
	    }
	    return false;
	}
	
	/**
	 * Retrieve quote
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote()
	{
		return Mage::getSingleton('checkout/session')->getQuote();
	}
	
	public function getCanDisplayWishlist()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn();
	}

	public function getCanDisplayCart()
	{
		return true;
	}
    
	public function getRemoveItemUrl($item)
	{
	    return $this->getUrl('checkout/cart/delete',array('id'=>$item->getId()));
	}
	
	public function getMoveToWishlistItemUrl($item)
	{
	    return $this->getUrl('checkout/cart/moveToWishlist',array('id'=>$item->getId()));
	}	
}// Class Mage_Wishlist_Block_Customer_Sidebar END
