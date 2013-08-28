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
 * Multiple wishlist helper
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Helper_Data extends Magento_Wishlist_Helper_Data
{
    /**
     * The list of default wishlists grouped by customer id
     *
     * @var array
     */
    protected $_defaultWishlistsByCustomer = array();

    /**
     * Create wishlist item collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        if ($this->isMultipleEnabled()) {
            return Mage::getModel('Magento_Wishlist_Model_Item')->getCollection()
                ->addCustomerIdFilter($this->getCustomer()->getId())
                ->addStoreFilter(Mage::app()->getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter();
        } else {
            return parent::_createWishlistItemCollection();
        }
    }

    /**
     * Check whether multiple wishlist is enabled
     *
     * @return bool
     */
    public function isMultipleEnabled()
    {
        return $this->isModuleOutputEnabled()
            && Mage::getStoreConfig('wishlist/general/active')
            && Mage::getStoreConfig('wishlist/general/multiple_enabled');
    }

    /**
     * Check whether given wishlist is default for it's customer
     *
     * @param Magento_Wishlist_Model_Wishlist $wishlist
     * @return bool
     */
    public function isWishlistDefault(Magento_Wishlist_Model_Wishlist $wishlist)
    {
        return $this->getDefaultWishlist($wishlist->getCustomerId())->getId() == $wishlist->getId();
    }

    /**
     * Retrieve customer's default wishlist
     *
     * @param int $customerId
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getDefaultWishlist($customerId = null)
    {
        if (!$customerId && $this->getCustomer()) {
            $customerId = $this->getCustomer()->getId();
        }
        if (!isset($this->_defaultWishlistsByCustomer[$customerId])) {
            $this->_defaultWishlistsByCustomer[$customerId] = Mage::getModel('Magento_Wishlist_Model_Wishlist');
            $this->_defaultWishlistsByCustomer[$customerId]->loadByCustomer($customerId, false);
        }
        return $this->_defaultWishlistsByCustomer[$customerId];
    }

    /**
     * Get max allowed number of wishlists per customers
     *
     * @return int
     */
    public function getWishlistLimit()
    {
        return Mage::getStoreConfig('wishlist/general/multiple_wishlist_number');
    }

    /**
     * Check whether given wishlist collection size exceeds wishlist limit
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection $wishlistList
     * @return bool
     */
    public function isWishlistLimitReached(Magento_Wishlist_Model_Resource_Wishlist_Collection $wishlistList)
    {
        return count($wishlistList) >= $this->getWishlistLimit();
    }

    /**
     * Retrieve Wishlist collection by customer id
     *
     * @param int $customerId
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getCustomerWishlists($customerId = null)
    {
        if (!$customerId && $this->getCustomer()) {
            $customerId = $this->getCustomer()->getId();
        }
        $wishlistsByCustomer = Mage::registry('wishlists_by_customer');
        if (!isset($wishlistsByCustomer[$customerId])) {
            $collection = Mage::getModel('Magento_Wishlist_Model_Wishlist')->getCollection();
            $collection->filterByCustomerId($customerId);
            $wishlistsByCustomer[$customerId] = $collection;
            Mage::register('wishlists_by_customer', $wishlistsByCustomer);
        }
        return $wishlistsByCustomer[$customerId];
    }

    /**
     * Retrieve number of wishlist items in given wishlist
     *
     * @param Magento_Wishlist_Model_Wishlist $wishlist
     * @return int
     */
    public function getWishlistItemCount(Magento_Wishlist_Model_Wishlist $wishlist)
    {
        $collection = $wishlist->getItemCollection();
        if (Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
            $count = $collection->getItemsQty();
        } else {
            $count = $collection->getSize();
        }
        return $count;
    }
}
