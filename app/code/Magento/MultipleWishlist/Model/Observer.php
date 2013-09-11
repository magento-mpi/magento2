<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist observer.
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model;

class Observer
{
    /**
     * Set collection of all items from all wishlists to wishlist helper
     * So all the information about number of items in wishlists will take all wishlist into account
     */
    public function initHelperItemCollection()
    {
        if (\Mage::helper('Magento\MultipleWishlist\Helper\Data')->isMultipleEnabled()) {
            $collection = \Mage::getModel('Magento\Wishlist\Model\Item')->getCollection()
                ->addCustomerIdFilter(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
                ->setVisibilityFilter()
                ->addStoreFilter(\Mage::app()->getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter();
            \Mage::helper('Magento\Wishlist\Helper\Data')->setWishlistItemCollection($collection);
        }
    }
}
