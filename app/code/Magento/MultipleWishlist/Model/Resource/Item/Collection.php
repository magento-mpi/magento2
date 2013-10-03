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
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Adminhtml\Helper\Sales $adminhtmlSales
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Date $date
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Core\Model\Resource $coreResource
     * @param \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollFactory
     * @param \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory
     * @param \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Wishlist\Model\Resource\Item $resource
     */
    public function __construct(
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Adminhtml\Helper\Sales $adminhtmlSales,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Date $date,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Core\Model\Resource $coreResource,
        \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollFactory,
        \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory,
        \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Wishlist\Model\Resource\Item $resource
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($catalogInventoryData, $adminhtmlSales, $eventManager, $logger, $fetchStrategy,
            $entityFactory, $storeManager, $date, $wishlistConfig, $productVisibility, $coreResource,
            $optionCollFactory, $productCollFactory, $catalogConfFactory, $catalogAttrFactory, $resource);
    }

    /**
     * Add filtration by customer id
     *
     * @param int $customerId
     * @return \Magento\MultipleWishlist\Model\Resource\Item\Collection
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
