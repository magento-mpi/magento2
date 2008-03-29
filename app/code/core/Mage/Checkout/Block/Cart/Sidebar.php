<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist sidebar block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Abstract
{
    protected $_items;
    protected $_subtotal;
    protected $_summaryCount;

    protected function _getCartInfo()
    {
        if (!is_null($this->_items)) {
            return;
        }
        $cart = Mage::getModel('checkout/cart')->getCartInfo();

        $this->_items = $cart->getItems();
        $this->_subtotal = $cart->getSubtotal();
        $this->_summaryCount = (Mage::getStoreConfig('checkout/cart_link/use_qty') ? $cart->getItemsQty() : $cart->getItemsCount())*1;

        usort($this->_items, array($this, 'sortByCreatedAt'));
    }

    public function getRecentItems()
    {
        $this->_getCartInfo();
        if (!$this->_items) {
            return array();
        }
        $i = 0;
        foreach ($this->_items as $quoteItem) {
            $item = clone $quoteItem;
//            $item->setItemProductThumbnail($this->helper('checkout')->getQuoteItemProductThumbnail($item));
//            $item->setItemProduct($this->helper('checkout')->getQuoteItemProduct($item));
//            $item->setProductUrl($this->helper('checkout')->getQuoteItemProductUrl($item));
//            $item->setProductName($this->helper('checkout')->getQuoteItemProductName($item));
//            $item->setProductDescription($this->helper('catalog/product')->getProductDescription($item));
            $items[] = $item;
            if (++$i==3) break;
        }
        return $items;
    }

    public function sortByCreatedAt($a, $b)
    {
        $a1 = $a->getCreatedAt();
        $b1 = $b->getCreatedAt();
        return $a1<$b1 ? 1 : $a1>$b1 ? -1 : 0;
    }

    public function getSubtotal()
    {
        $this->_getCartInfo();
        return $this->_subtotal;
    }

    public function getSummaryCount()
    {
        $this->_getCartInfo();
        return $this->_summaryCount;
    }

    public function getCanDisplayCart()
    {
        return true;
    }

    public function getRemoveItemUrl($item)
    {
        return $this->helper('checkout/cart')->getRemoveUrl($item);
    }

    public function getMoveToWishlistItemUrl($item)
    {
        return $this->getUrl('checkout/cart/moveToWishlist',array('id'=>$item->getId()));
    }

    public function getIncExcTax($flag)
    {
        $text = Mage::helper('tax')->getIncExcText($flag);
        return $text ? ' ('.$text.')' : '';
    }
}