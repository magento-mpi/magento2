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
 * Multiple wishlist item resource collection
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model\Resource\Item;

class Collection extends \Magento\Wishlist\Model\Resource\Item\Collection
{
    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Sales\Helper\Admin $adminhtmlSales
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Stdlib\DateTime\DateTime $date
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\App\Resource $coreResource
     * @param \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollectionFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory
     * @param \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory
     * @param \Magento\Wishlist\Model\Resource\Item $resource
     * @param \Magento\App\State $appState
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param mixed $connection
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Sales\Helper\Admin $adminhtmlSales,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Stdlib\DateTime\DateTime $date,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\App\Resource $coreResource,
        \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollectionFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory,
        \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory,
        \Magento\Wishlist\Model\Resource\Item $resource,
        \Magento\App\State $appState,
        \Magento\Wishlist\Helper\Data $wishlistData,
        $connection = null
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $catalogInventoryData,
            $adminhtmlSales,
            $storeManager,
            $date,
            $wishlistConfig,
            $productVisibility,
            $coreResource,
            $optionCollectionFactory,
            $productCollectionFactory,
            $catalogConfFactory,
            $catalogAttrFactory,
            $resource,
            $appState,
            $connection
        );
    }

    /**
     * Add filtration by customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerIdFilter($customerId)
    {
        parent::addCustomerIdFilter($customerId);

        $adapter = $this->getConnection();
        $defaultWishlistName = $this->_wishlistData->getDefaultWishlistName();
        $this->getSelect()->columns(
            array('wishlist_name' => $adapter->getIfNullSql('wishlist.name', $adapter->quote($defaultWishlistName)))
        );

        $this->addFilterToMap(
            'wishlist_name', $adapter->getIfNullSql('wishlist.name', $adapter->quote($defaultWishlistName))
        );
        return $this;
    }
}
