<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist observer.
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Model_Observer
{
    /**
     * Wishlist data
     *
     * @var Enterprise_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Enterprise_Wishlist_Helper_Data $wishlistData
     */
    public function __construct(
        Enterprise_Wishlist_Helper_Data $wishlistData
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
