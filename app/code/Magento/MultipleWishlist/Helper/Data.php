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
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Item collection factory
     *
     * @var \Magento\Wishlist\Model\Resource\Item\CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Wishlist factory
     *
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * Wishlist collection factory
     *
     * @var \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory
    ) {
        $this->_wishlistFactory = $wishlistFactory;
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        parent::__construct($eventManager, $coreData, $context, $coreRegistry, $coreStoreConfig);
    }

    /**
     * Create wishlist item collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
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
        $wishlistsByCustomer = $this->_coreRegistry->registry('wishlists_by_customer');
        if (!isset($wishlistsByCustomer[$customerId])) {
            /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $collection */
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
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return int
     */
    public function getWishlistItemCount(\Magento\Wishlist\Model\Wishlist $wishlist)
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
