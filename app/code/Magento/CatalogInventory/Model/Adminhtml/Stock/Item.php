<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Adminhtml\Stock;

use Magento\Customer\Api\GroupManagementInterface;

/**
 * Catalog Inventory Stock Model for adminhtml area
 */
class Item extends \Magento\CatalogInventory\Model\Stock\Item
{
    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CatalogInventory\Model\Indexer\Stock\Processor $stockIndexerProcessor
     * @param \Magento\CatalogInventory\Model\Stock\Status $stockStatus
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     * @param ItemRegistry $stockItemRegistry
     * @param \Magento\CatalogInventory\Helper\Minsaleqty $catalogInventoryMinsaleqty
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Math\Division $mathDivision
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param GroupManagementInterface $groupManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogInventory\Model\Indexer\Stock\Processor $stockIndexerProcessor,
        \Magento\CatalogInventory\Model\Stock\Status $stockStatus,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        \Magento\CatalogInventory\Model\Stock\ItemRegistry $stockItemRegistry,
        \Magento\CatalogInventory\Helper\Minsaleqty $catalogInventoryMinsaleqty,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Math\Division $mathDivision,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $customerSession,
            $stockIndexerProcessor,
            $stockStatus,
            $stockItemService,
            $stockItemRegistry,
            $catalogInventoryMinsaleqty,
            $scopeConfig,
            $storeManager,
            $localeFormat,
            $mathDivision,
            $localeDate,
            $productFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->groupManagement = $groupManagement;
    }

    /**
     * Getter for customer group id, return default group if not set
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->_customerGroupId === null) {
            return $this->groupManagement->getAllGroup()->getId();
        }
        return parent::getCustomerGroupId();
    }

    /**
     * Check if qty check can be skipped. Skip checking in adminhtml area
     *
     * @return bool
     */
    protected function _isQtyCheckApplicable()
    {
        return true;
    }

    /**
     * Check if notification message should be added despite of backorders notification flag
     *
     * @return bool
     */
    protected function _hasDefaultNotificationMessage()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasAdminArea()
    {
        return true;
    }
}
