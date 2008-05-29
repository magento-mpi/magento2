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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Get array last added items
     *
     * @return array
     */
    public function getRecentItems()
    {
        $items = array();
        $i = 0;
        $allItems = array_reverse($this->getItems());
        foreach ($allItems as $item) {
        	$items[] = $item;
        	if (++$i==3) break;
        }
        return $items;
    }

    /**
     * Get shopping cart subtotal
     *
     * @return decimal
     */
    public function getSubtotal()
    {
        $totals = $this->getTotals();
        if (isset($totals['subtotal'])) {
            return $totals['subtotal']->getValue();
        }
        return 0;
    }

    public function getSummaryCount()
    {
        return Mage::getSingleton('checkout/cart')->getSummaryQty();
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

    public function isPossibleOnepageCheckout()
    {
        return $this->helper('checkout')->canOnepageCheckout();
    }
}