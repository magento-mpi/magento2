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
class Magento_MultipleWishlist_Model_Observer
{
    /**
     * Wishlist data
     *
     * @var Magento_MultipleWishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_MultipleWishlist_Helper_Data $wishlistData
     */
    public function __construct(
        Magento_MultipleWishlist_Helper_Data $wishlistData
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
            $collection = Mage::getModel('Magento_Wishlist_Model_Item')->getCollection()
                ->addCustomerIdFilter(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
                ->setVisibilityFilter()
                ->addStoreFilter(Mage::app()->getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter();
            $this->_wishlistData->setWishlistItemCollection($collection);
        }
    }
}
