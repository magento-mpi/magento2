<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Account dashboard sidebar
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Customer\Block\Account\Dashboard;

class Sidebar extends \Magento\Core\Block\Template
{
    protected $_cartItemsCount;

    /**
     * Enter description here...
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    protected $_compareItems;

    public function getShoppingCartUrl()
    {
        return \Mage::getUrl('checkout/cart');
    }

    public function getCartItemsCount()
    {
        if( !$this->_cartItemsCount ) {
            $this->_cartItemsCount = \Mage::getModel('Magento\Sales\Model\Quote')
                ->setId(\Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote()->getId())
                ->getItemsCollection()
                ->getSize();
        }

        return $this->_cartItemsCount;
    }

    public function getWishlist()
    {
        if( !$this->_wishlist ) {
            $this->_wishlist = \Mage::getModel('Magento\Wishlist\Model\Wishlist')
                ->loadByCustomer(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer());
            $this->_wishlist->getItemCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
                ->addAttributeToFilter('store_id', array('in' => $this->_wishlist->getSharedStoreIds()))
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
        return \Mage::getUrl('wishlist/index/cart', array('item' => $wishlistItem->getId()));
    }

    public function getCompareItems()
    {
        if( !$this->_compareItems ) {
            $this->_compareItems =
                \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Compare\Item\Collection')
                    ->setStoreId(\Mage::app()->getStore()->getId());
            $this->_compareItems->setCustomerId(
                \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId()
            );
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
