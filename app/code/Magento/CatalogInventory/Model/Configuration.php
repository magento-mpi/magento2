<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogInventory\Model;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Helper\Minsaleqty as MinsaleqtyHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Configuration
 */
class Configuration implements StockConfigurationInterface
{
    /**
     * Default website id
     */
    const DEFAULT_WEBSITE_ID = 1;

    /**
     * Inventory options config path
     */
    const XML_PATH_GLOBAL = 'cataloginventory/options/';

    /**
     * Subtract config path
     */
    const XML_PATH_CAN_SUBTRACT = 'cataloginventory/options/can_subtract';

    /**
     * Back in stock config path
     */
    const XML_PATH_CAN_BACK_IN_STOCK = 'cataloginventory/options/can_back_in_stock';

    /**
     * Item options config path
     */
    const XML_PATH_ITEM = 'cataloginventory/item_options/';

    /**
     * Max qty config path
     */
    const XML_PATH_MIN_QTY = 'cataloginventory/item_options/min_qty';

    /**
     * Min sale qty config path
     */
    const XML_PATH_MIN_SALE_QTY = 'cataloginventory/item_options/min_sale_qty';

    /**
     * Max sale qty config path
     */
    const XML_PATH_MAX_SALE_QTY = 'cataloginventory/item_options/max_sale_qty';

    /**
     * Back orders config path
     */
    const XML_PATH_BACKORDERS = 'cataloginventory/item_options/backorders';

    /**
     * Notify stock config path
     */
    const XML_PATH_NOTIFY_STOCK_QTY = 'cataloginventory/item_options/notify_stock_qty';

    /**
     * Manage stock config path
     */
    const XML_PATH_MANAGE_STOCK = 'cataloginventory/item_options/manage_stock';

    /**
     * Enable qty increments config path
     */
    const XML_PATH_ENABLE_QTY_INCREMENTS = 'cataloginventory/item_options/enable_qty_increments';

    /**
     * Qty increments config path
     */
    const XML_PATH_QTY_INCREMENTS = 'cataloginventory/item_options/qty_increments';

    /**
     * Show out of stock config path
     */
    const XML_PATH_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';

    /**
     * Auto return config path
     */
    const XML_PATH_ITEM_AUTO_RETURN = 'cataloginventory/item_options/auto_return';

    /**
     * Path to configuration option 'Display product stock status'
     */
    const XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS = 'cataloginventory/options/display_product_stock_status';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MinsaleqtyHelper
     */
    protected $minsaleqtyHelper;

    /**
     * All product types registry in scope of quantity availability
     *
     * @var array
     */
    protected $isQtyTypeIds;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ConfigInterface $config
     * @param ScopeConfigInterface $scopeConfig
     * @param MinsaleqtyHelper $minsaleqtyHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ConfigInterface $config,
        ScopeConfigInterface $scopeConfig,
        MinsaleqtyHelper $minsaleqtyHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->minsaleqtyHelper = $minsaleqtyHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve Default Website ID
     *
     * @return int
     */
    public function getDefaultWebsiteId()
    {
        return (int) $this->storeManager->getWebsite(true)->getId();
    }

    /**
     * @param int|null $filter
     * @return array
     */
    public function getIsQtyTypeIds($filter = null)
    {
        if (null === $this->isQtyTypeIds) {
            $this->isQtyTypeIds = [];
            foreach ($this->config->getAll() as $typeId => $typeConfig) {
                $this->isQtyTypeIds[$typeId] = isset($typeConfig['is_qty']) ? $typeConfig['is_qty'] : false;
            }
        }
        $result = $this->isQtyTypeIds;
        if ($filter !== null) {
            foreach ($result as $key => $value) {
                if ($value !== $filter) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

    /**
     * @param int $productTypeId
     * @return bool
     */
    public function isQty($productTypeId)
    {
        $result = $this->getIsQtyTypeIds();
        return isset($result[$productTypeId]) ? $result[$productTypeId] : false;
    }

    /**
     * Check if is possible subtract value from item qty
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function canSubtractQty($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CAN_SUBTRACT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return float
     */
    public function getMinQty($store = null)
    {
        return (float)$this->scopeConfig->getValue(
            self::XML_PATH_MIN_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @param int $customerGroupId
     * @return float
     */
    public function getMinSaleQty($store = null, $customerGroupId = null)
    {
        return (float)$this->minsaleqtyHelper->getConfigValue($customerGroupId, $store);
    }

    /**
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return float|null
     */
    public function getMaxSaleQty($store = null)
    {
        return (float)$this->scopeConfig->getValue(
            self::XML_PATH_MAX_SALE_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return float
     */
    public function getNotifyStockQty($store = null)
    {
        return (float) $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFY_STOCK_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve whether Quantity Increments is enabled
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function getEnableQtyIncrements($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_QTY_INCREMENTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getQtyIncrements($store = null)
    {
        return (float)$this->scopeConfig->getValue(
            self::XML_PATH_QTY_INCREMENTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve backorders status
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getBackorders($store = null)
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_BACKORDERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve Manage Stock data wrapper
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getManageStock($store = null)
    {
        return (int) $this->scopeConfig->isSetFlag(
            self::XML_PATH_MANAGE_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve can Back in stock
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function getCanBackInStock($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CAN_BACK_IN_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Display out of stock products option
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isShowOutOfStock($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_OUT_OF_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if credit memo items auto return option is enabled
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isAutoReturnEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ITEM_AUTO_RETURN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get 'Display product stock status' option value
     * Shows if it is necessary to show product stock status ('in stock'/'out of stock')
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isDisplayProductStockStatus($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $field
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string|null
     */
    public function getDefaultConfigValue($field, $store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ITEM . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve inventory item options (used in config)
     *
     * @return string[]
     */
    public function getConfigItemOptions()
    {
        return [
            'min_qty',
            'backorders',
            'min_sale_qty',
            'max_sale_qty',
            'notify_stock_qty',
            'manage_stock',
            'enable_qty_increments',
            'qty_increments',
            'is_decimal_divided'
        ];
    }
}
