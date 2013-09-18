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
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     */
    public function __construct(
        \Magento\MultipleWishlist\Helper\Data $wishlistData
    ) {
        $this->_wishlistData = $wishlistData;
    }

    /**
     * Set collection of all items from all wishlists to wishlist helper
     * So all the information about number of items in wishlists will take all wishlist into account
     */
    public function initHelperItemCollection()
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            $collection = \Mage::getModel('Magento\Wishlist\Model\Item')->getCollection()
                ->addCustomerIdFilter(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
                ->setVisibilityFilter()
                ->addStoreFilter(\Mage::app()->getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter();
            $this->_wishlistData->setWishlistItemCollection($collection);
        }
    }
}
