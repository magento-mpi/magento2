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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Collection constructor
     *
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Adminhtml\Helper\Sales $adminhtmlSales
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Wishlist\Model\Resource\Item $resource
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Adminhtml\Helper\Sales $adminhtmlSales,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Wishlist\Model\Resource\Item $resource
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $wishlistData, $catalogInventoryData, $adminhtmlSales, $eventManager, $fetchStrategy, $resource
        );
    }

    /**
     * Initialize db select
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerIdFilter($this->_coreRegistry->registry('current_customer')->getId())
            ->resetSortOrder()
            ->addDaysInWishlist()
            ->addStoreData();
        return $this;
    }
}
