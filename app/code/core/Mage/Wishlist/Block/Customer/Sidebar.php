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

class Mage_Wishlist_Block_Customer_Sidebar extends Mage_Core_Block_Template
{
	protected  $_wishlist = null;

	public function getWishlistItems()
	{
		return $this->getWishlist()->getProductCollection();
	}

	public function getWishlist()
	{
		if(is_null($this->_wishlist)) {
			$this->_wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());

			$this->_wishlist->getProductCollection()
				->addAttributeToSelect('name')
				->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
				->addAttributeToFilter('store_id', array('in'=>$this->_wishlist->getSharedStoreIds()))
				->addAttributeToSort('added_at', 'desc')
                ->setCurPage(1)
				->setPageSize(3)
				->load();
		}

		return $this->_wishlist;
	}

	public function toHtml()
	{
        if( sizeof($this->getWishlistItems()->getItems()) > 0 ){
        	return parent::toHtml();
        } else {
            return '';
        }
	}

	public function getCanDisplayWishlist()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn();
	}

	public function getRemoveItemUrl($item)
	{
	    return $this->getUrl('wishlist/index/remove',array('item'=>$item->getWishlistItemId()));
	}

	public function getAddToCartItemUrl($item)
	{
	    return $this->getUrl('wishlist/index/cart',array('item'=>$item->getWishlistItemId()));
	}
}
