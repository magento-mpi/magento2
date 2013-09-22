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
namespace Magento\Wishlist\Model\Resource\Item\Collection;

class Grid extends \Magento\Wishlist\Model\Resource\Item\Collection
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * @param \Magento\Adminhtml\Helper\Sales $adminhtmlSales
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\Wishlist\Model\Resource\Item $resource
     */
    public function __construct(
        \Magento\Adminhtml\Helper\Sales $adminhtmlSales,
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        \Magento\Core\Model\Registry $registry,
        \Magento\Wishlist\Model\Resource\Item $resource
    ) {
        $this->_registryManager = $registry;
        parent::__construct(
            $catalogInventoryData, $adminhtmlSales, $eventManager, $logger, $fetchStrategy, $entityFactory, $resource
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
     * @return  \Magento\Data\Collection\Db
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
     * @return \Magento\Data\Collection\Db
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
