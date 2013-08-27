<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist item resource collection
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Model_Resource_Item_Collection extends Magento_Wishlist_Model_Resource_Item_Collection
{
    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * Collection constructor
     *
     *
     *
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($catalogInventoryData, $adminhtmlSales, $fetchStrategy, $resource);
    }

    /**
     * Add filtration by customer id
     *
     * @param int $customerId
     * @return Enterprise_Wishlist_Model_Resource_Item_Collection
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
