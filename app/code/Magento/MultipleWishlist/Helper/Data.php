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
 * Multiple wishlist helper
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Helper;

class Data extends \Magento\Wishlist\Helper\Data
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
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _createWishlistItemCollection()
    {
        if ($this->isMultipleEnabled()) {
            return \Mage::getModel('Magento\Wishlist\Model\Item')->getCollection()
                ->addCustomerIdFilter($this->getCustomer()->getId())
                ->addStoreFilter(\Mage::app()->getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter();
        } else {
            return parent::_createWishlistItemCollection();
        }
    }

    /**
     * Retrieve current customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return \Mage::helper('Magento\Wishlist\Helper\Data')->getCustomer();
    }

    /**
     * Check whether multiple wishlist is enabled
     *
     * @return bool
     */
    public function isMultipleEnabled()
    {
        return $this->isModuleOutputEnabled()
            && \Mage::getStoreConfig('wishlist/general/active')
            && \Mage::getStoreConfig('wishlist/general/multiple_enabled');
    }

    /**
     * Check whether given wishlist is default for it's customer
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return bool
     */
    public function isWishlistDefault(\Magento\Wishlist\Model\Wishlist $wishlist)
    {
        return $this->getDefaultWishlist($wishlist->getCustomerId())->getId() == $wishlist->getId();
    }

    /**
     * Retrieve customer's default wishlist
     *
     * @param int $customerId
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist($customerId = null)
    {
        if (!$customerId && $this->getCustomer()) {
            $customerId = $this->getCustomer()->getId();
        }
        if (!isset($this->_defaultWishlistsByCustomer[$customerId])) {
            $this->_defaultWishlistsByCustomer[$customerId] = \Mage::getModel('Magento\Wishlist\Model\Wishlist');
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
        return \Mage::getStoreConfig('wishlist/general/multiple_wishlist_number');
    }

    /**
     * Check whether given wishlist collection size exceeds wishlist limit
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlistList
     * @return bool
     */
    public function isWishlistLimitReached(\Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlistList)
    {
        return count($wishlistList) >= $this->getWishlistLimit();
    }

    /**
     * Retrieve Wishlist collection by customer id
     *
     * @param int $customerId
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getCustomerWishlists($customerId = null)
    {
        if (!$customerId && $this->getCustomer()) {
            $customerId = $this->getCustomer()->getId();
        }
        $wishlistsByCustomer = \Mage::registry('wishlists_by_customer');
        if (!isset($wishlistsByCustomer[$customerId])) {
            $collection = \Mage::getModel('Magento\Wishlist\Model\Wishlist')->getCollection();
            $collection->filterByCustomerId($customerId);
            $wishlistsByCustomer[$customerId] = $collection;
            \Mage::register('wishlists_by_customer', $wishlistsByCustomer);
        }
        return $wishlistsByCustomer[$customerId];
    }

    /**
     * Retrieve number of wishlist items in given wishlist
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return int
     */
    public function getWishlistItemCount(\Magento\Wishlist\Model\Wishlist $wishlist)
    {
        $collection = $wishlist->getItemCollection();
        if (\Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
            $count = $collection->getItemsQty();
        } else {
            $count = $collection->getSize();
        }
        return $count;
    }
}
