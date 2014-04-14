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
 * Wishlist item collection filtered by customer
 */
namespace Magento\MultipleWishlist\Model\Item;

class Collection extends \Magento\MultipleWishlist\Model\Resource\Item\Collection
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Sales\Helper\Admin $adminhtmlSales
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Stdlib\DateTime\DateTime $date
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Framework\App\Resource $coreResource
     * @param \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollectionFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory
     * @param \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory
     * @param \Magento\Wishlist\Model\Resource\Item $resource
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Registry $coreRegistry
     * @param mixed $connection
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Sales\Helper\Admin $adminhtmlSales,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Stdlib\DateTime\DateTime $date,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\App\Resource $coreResource,
        \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollectionFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory,
        \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory,
        \Magento\Wishlist\Model\Resource\Item $resource,
        \Magento\Framework\App\State $appState,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Registry $coreRegistry,
        $connection = null
    ) {
        $this->_coreRegistry = $coreRegistry;
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
            $wishlistData,
            $connection
        );
    }

    /**
     * Initialize db select
     *
     * @return \Magento\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerIdFilter(
            $this->_coreRegistry->registry('current_customer')->getId()
        )->resetSortOrder()->addDaysInWishlist()->addStoreData();
        return $this;
    }
}
