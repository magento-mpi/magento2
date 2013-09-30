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
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_MultipleWishlist_Helper_Data extends Magento_Wishlist_Helper_Data
{
    /**
     * The list of default wishlists grouped by customer id
     *
     * @var array
     */
    protected $_defaultWishlistsByCustomer = array();

    /**
     * Item collection factory
     *
     * @var Magento_Wishlist_Model_Resource_Item_CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Wishlist collection factory
     *
     * @var Magento_Wishlist_Model_Resource_Wishlist_CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Customer_Model_SessionProxy $customerSession
     * @param Magento_Wishlist_Model_WishlistFactory $wishlistFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Wishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory
     * @param Magento_Wishlist_Model_Resource_Wishlist_CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Customer_Model_SessionProxy $customerSession,
        Magento_Wishlist_Model_WishlistFactory $wishlistFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Wishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory,
        Magento_Wishlist_Model_Resource_Wishlist_CollectionFactory $wishlistCollectionFactory
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        parent::__construct($eventManager, $coreData, $context, $coreRegistry, $coreStoreConfig, $customerSession,
            $wishlistFactory, $storeManager);
    }

    /**
     * Create wishlist item collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        if ($this->isMultipleEnabled()) {
            return $this->_itemCollectionFactory->create()
                ->addCustomerIdFilter($this->getCustomer()->getId())
                ->addStoreFilter($this->_storeManager->getWebsite()->getStoreIds())
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
            && $this->_coreStoreConfig->getConfig('wishlist/general/active')
            && $this->_coreStoreConfig->getConfig('wishlist/general/multiple_enabled');
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
            $this->_defaultWishlistsByCustomer[$customerId] = $this->_wishlistFactory->create();
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
        return $this->_coreStoreConfig->getConfig('wishlist/general/multiple_wishlist_number');
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
        $wishlistsByCustomer = $this->_coreRegistry->registry('wishlists_by_customer');
        if (!isset($wishlistsByCustomer[$customerId])) {
            /** @var Magento_Wishlist_Model_Resource_Wishlist_Collection $collection */
            $collection = $this->_wishlistCollectionFactory->create();
            $collection->filterByCustomerId($customerId);
            $wishlistsByCustomer[$customerId] = $collection;
            $this->_coreRegistry->register('wishlists_by_customer', $wishlistsByCustomer);
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
        if ($this->_coreStoreConfig->getConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
            $count = $collection->getItemsQty();
        } else {
            $count = $collection->getSize();
        }
        return $count;
    }
}
