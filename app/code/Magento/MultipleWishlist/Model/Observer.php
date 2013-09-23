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
 * @SuppressWarnings(PHPMD.LongVariable)
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
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Item collection factory
     *
     * @var Magento_Wishlist_Model_Resource_Item_CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_MultipleWishlist_Helper_Data $wishlistData
     * @param Magento_Wishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_MultipleWishlist_Helper_Data $wishlistData,
        Magento_Wishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
    }

    /**
     * Set collection of all items from all wishlists to wishlist helper
     * So all the information about number of items in wishlists will take all wishlist into account
     */
    public function initHelperItemCollection()
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            /** @var Magento_Wishlist_Model_Resource_Item_Collection $collection */
            $collection = $this->_itemCollectionFactory->create();
            $collection->addCustomerIdFilter($this->_customerSession->getCustomerId())
                ->setVisibilityFilter()
                ->addStoreFilter($this->_storeManager->getWebsite()->getStoreIds())
                ->setVisibilityFilter();
            $this->_wishlistData->setWishlistItemCollection($collection);
        }
    }
}
