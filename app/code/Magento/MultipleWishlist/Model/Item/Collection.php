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
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Collection constructor
     *
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Wishlist_Model_Resource_Item $resource
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Wishlist_Model_Resource_Item $resource
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
