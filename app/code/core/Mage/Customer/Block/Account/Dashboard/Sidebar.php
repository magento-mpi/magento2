<?php
/**
 * Account dashboard sidebar
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Customer_Block_Account_Dashboard_Sidebar extends Mage_Core_Block_Template
{
    protected $_cartItemsCount;
    protected $_wishlist;
    protected $_compareItems;

    public function getShoppingCartUrl()
    {
        return Mage::getUrl('checkout/cart');
    }

    public function getCartItemsCount()
    {
        if( !$this->_cartItemsCount ) {
            $this->_cartItemsCount = Mage::getModel('sales/quote')
                ->setId(Mage::getModel('checkout/session')->getQuote()->getId())
                ->getItemsCollection()
                ->getSize();
        }

        return $this->_cartItemsCount;
    }

	public function getWishlist()
	{
		if( !$this->_wishlist ) {
			$this->_wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());

			$this->_wishlist->getItemCollection()
				->addAttributeToSelect('name')
				->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
				->addAttributeToFilter('store_id', array('in'=>Mage::getSingleton('core/store')->getDatashareStores('wishlist')))
				->addAttributeToSort('added_at', 'desc')
                ->setCurPage(1)
				->setPageSize(3)
				->load();
		}

		return $this->_wishlist->getItemCollection();
	}

	public function getWishlistCount()
	{
	    return $this->getWishlist()->getSize();
	}

	public function getWishlistAddToCartLink($wishlistItem)
	{
	    return Mage::getUrl('wishlist/index/cart', array('item' => $wishlistItem->getId()));
	}

 	public function getCompareItems()
 	{
 		if( !$this->_compareItems ) {
 			$this->_compareItems = Mage::getResourceModel('catalog/product_compare_item_collection')
 				->setStoreId(Mage::getSingleton('core/store')->getId());
			$this->_compareItems->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
			$this->_compareItems
				->addAttributeToSelect('name')
				->useProductItem()
				->load();

 		}

 		return $this->_compareItems;
 	}

 	public function getCompareJsObjectName()
 	{
 		return "dashboardSidebarCompareJsObject";
 	}

 	public function getCompareRemoveUrlTemplate()
 	{
 		return $this->getUrl('catalog/product_compare/remove',array('product'=>'#{id}'));
 	}

 	public function getCompareAddUrlTemplate()
 	{
 		return $this->getUrl('catalog/product_compare/add',array('product'=>'#{id}'));
 	}

 	public function getCompareUrl()
 	{
 		return $this->getUrl('catalog/product_compare');
 	}
}