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
class Magento_MultipleWishlist_Model_Resource_Item_Collection extends Magento_Wishlist_Model_Resource_Item_Collection
{
    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Date $date
     * @param Magento_Wishlist_Model_Config $wishlistConfig
     * @param Magento_Catalog_Model_Product_Visibility $productVisibility
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Wishlist_Model_Resource_Item_Option_CollectionFactory $optionCollFactory
     * @param Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollFactory
     * @param Magento_Catalog_Model_Resource_ConfigFactory $catalogConfFactory
     * @param Magento_Catalog_Model_Entity_AttributeFactory $catalogAttrFactory
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Wishlist_Model_Resource_Item $resource
     */
    public function __construct(
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Date $date,
        Magento_Wishlist_Model_Config $wishlistConfig,
        Magento_Catalog_Model_Product_Visibility $productVisibility,
        Magento_Core_Model_Resource $coreResource,
        Magento_Wishlist_Model_Resource_Item_Option_CollectionFactory $optionCollFactory,
        Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollFactory,
        Magento_Catalog_Model_Resource_ConfigFactory $catalogConfFactory,
        Magento_Catalog_Model_Entity_AttributeFactory $catalogAttrFactory,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Wishlist_Model_Resource_Item $resource
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
     * @return Magento_MultipleWishlist_Model_Resource_Item_Collection
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
