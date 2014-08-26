<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Model;

/**
 * Multiple wishlist observer.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Observer
{
    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Item collection factory
     *
     * @var \Magento\Wishlist\Model\Resource\Item\CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\MultipleWishlist\Helper\Data $wishlistData,
        \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
    }

    /**
     * Set collection of all items from all wishlists to wishlist helper
     * So all the information about number of items in wishlists will take all wishlist into account
     *
     * @return void
     */
    public function initHelperItemCollection()
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            /** @var \Magento\Wishlist\Model\Resource\Item\Collection $collection */
            $collection = $this->_itemCollectionFactory->create();
            $collection->addCustomerIdFilter(
                $this->_customerSession->getCustomerId()
            )->setVisibilityFilter()->addStoreFilter(
                $this->_storeManager->getWebsite()->getStoreIds()
            )->setVisibilityFilter();
            $this->_wishlistData->setWishlistItemCollection($collection);
        }
    }
}
