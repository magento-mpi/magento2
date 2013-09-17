<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item collection grouped by customer id
 */
class Magento_Wishlist_Model_Resource_Item_Collection_Grid extends Magento_Wishlist_Model_Resource_Item_Collection
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Wishlist_Model_Resource_Item $resource
     */
    public function __construct(
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Registry $registry,
        Magento_Wishlist_Model_Resource_Item $resource
    ) {
        $this->_registryManager = $registry;
        parent::__construct($catalogInventoryData, $adminhtmlSales, $eventManager, $fetchStrategy, $resource);
    }

    /**
     * Initialize db select
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerIdFilter($this->_registryManager->registry('current_customer')->getId())
        ->resetSortOrder()
        ->addDaysInWishlist()
        ->addStoreData();
        return $this;
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Magento_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'product_name') {
            return $this->setOrderByProductName($direction);
        } else {
            if ($field == 'days_in_wishlist') {
                $field = 'added_at';
                $direction = $direction == self::SORT_ORDER_DESC ? self::SORT_ORDER_ASC : self::SORT_ORDER_DESC;
            }
            return parent::setOrder($field, $direction);
        }
    }

    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return Magento_Data_Collection_Db
     */
    public function addFieldToFilter($field, $condition = null)
    {
        switch ($field) {
            case 'product_name':
                $value = (string) $condition['like'];
                $value = trim(trim($value, "'"), "%");
                return $this->addProductNameFilter($value);
            case 'store_id':
                if (isset($condition['eq'])) {
                    return $this->addStoreFilter($condition);
                }
                break;
            case 'days_in_wishlist':
                if (!isset($condition['datetime'])) {
                    return $this->addDaysFilter($condition);
                }
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
