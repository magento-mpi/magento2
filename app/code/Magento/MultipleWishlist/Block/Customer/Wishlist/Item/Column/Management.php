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
 * Wishlist item management column (copy, move, etc.)
 */
class Magento_MultipleWishlist_Block_Customer_Wishlist_Item_Column_Management
    extends Magento_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($coreRegistry, $wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Render block
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_wishlistData->isMultipleEnabled();
    }

    /**
     * Retrieve current customer wishlist collection
     *
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getWishlists()
    {
        return $this->_wishlistData->getCustomerWishlists();
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getDefaultWishlist()
    {
        return $this->_wishlistData->getDefaultWishlist();
    }

    /**
     * Retrieve current wishlist
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getCurrentWishlist()
    {
        return $this->_wishlistData->getWishlist();
    }

    /**
     * Check whether user multiple wishlist limit reached
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection $wishlists
     * @return bool
     */
    public function canCreateWishlists(Magento_Wishlist_Model_Resource_Wishlist_Collection $wishlists)
    {
        $customerId = $this->_customerSession->getCustomerId();
        return !$this->_wishlistData->isWishlistLimitReached($wishlists) && $customerId;
    }

    /**
     * Get wishlist item copy url
     *
     * @return string
     */
    public function getCopyItemUrl()
    {
        return $this->getUrl('wishlist/index/copyitem');
    }
}
