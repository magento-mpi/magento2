<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

/**
 * Stock item data object
 */
class StockItem extends AbstractObject
{
    /**#@+
     * Constants for Data Object keys
     */
    const ITEM_ID = 'item_id';

    const PRODUCT_ID = 'product_id';

    const STOCK_ID = 'stock_id';

    const QTY = 'qty';

    const MIN_QTY = 'min_qty';

    const USE_CONFIG_MIN_QTY = 'use_config_min_qty';

    const IS_QTY_DECIMAL = 'is_qty_decimal';

    const BACKORDERS = 'backorders';

    const USE_CONFIG_BACKORDERS = 'use_config_backorders';

    const MIN_SALE_QTY = 'min_sale_qty';

    const USE_CONFIG_MIN_SALE_QTY = 'use_config_min_sale_qty';

    const MAX_SALE_QTY = 'max_sale_qty';

    const USE_CONFIG_MAX_SALE_QTY = 'use_config_max_sale_qty';

    const IS_IN_STOCK = 'is_in_stock';

    const LOW_STOCK_DATE = 'low_stock_date';

    const NOTIFY_STOCK_QTY = 'notify_stock_qty';

    const USE_CONFIG_NOTIFY_STOCK_QTY = 'use_config_notify_stock_qty';

    const MANAGE_STOCK = 'manage_stock';

    const USE_CONFIG_MANAGE_STOCK = 'use_config_manage_stock';

    const STOCK_STATUS_CHANGED_AUTO = 'stock_status_changed_auto';

    const USE_CONFIG_QTY_INCREMENTS = 'use_config_qty_increments';

    const QTY_INCREMENTS = 'qty_increments';

    const USE_CONFIG_ENABLE_QTY_INC = 'use_config_enable_qty_inc';

    const ENABLE_QTY_INCREMENTS = 'enable_qty_increments';

    const IS_DECIMAL_DIVIDED = 'is_decimal_divided';

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * @return int
     */
    public function getStockId()
    {
        return $this->_get(self::STOCK_ID);
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * @return int
     */
    public function getMinQty()
    {
        return $this->_get(self::MIN_QTY);
    }

    /**
     * @return bool
     */
    public function canUseConfigMinQty()
    {
        return $this->_get(self::USE_CONFIG_MIN_QTY);
    }

    /**
     * @return bool
     */
    public function isQtyDecimal()
    {
        return $this->_get(self::IS_QTY_DECIMAL);
    }

    /**
     * @return int
     */
    public function getBackorders()
    {
        return $this->_get(self::BACKORDERS);
    }

    /**
     * @return bool
     */
    public function getUseConfigBackorders()
    {
        return $this->_get(self::USE_CONFIG_BACKORDERS);
    }

    /**
     * @return int
     */
    public function getMinSaleQty()
    {
        return $this->_get(self::MIN_SALE_QTY);
    }

    /**
     * @return bool
     */
    public function canUseConfigMinSaleQty()
    {
        return $this->_get(self::USE_CONFIG_MIN_SALE_QTY);
    }

    /**
     * @return int
     */
    public function getMaxSaleQty()
    {
        return $this->_get(self::MAX_SALE_QTY);
    }

    /**
     * @return bool
     */
    public function canUseConfigMaxSaleQty()
    {
        return $this->_get(self::USE_CONFIG_MAX_SALE_QTY);
    }

    /**
     * @return bool
     */
    public function isInStock()
    {
        return $this->_get(self::IS_IN_STOCK);
    }

    /**
     * @return string
     */
    public function getLowStockDate()
    {
        return $this->_get(self::LOW_STOCK_DATE);
    }

    /**
     * @return bool
     */
    public function getNotifyStockQty()
    {
        return $this->_get(self::NOTIFY_STOCK_QTY);
    }

    /**
     * @return bool
     */
    public function getUseConfigNotifyStockQty()
    {
        return $this->_get(self::USE_CONFIG_NOTIFY_STOCK_QTY);
    }

    /**
     * @return bool
     */
    public function getManageStock()
    {
        return $this->_get(self::MANAGE_STOCK);
    }

    /**
     * @return bool
     */
    public function canUseConfigManageStock()
    {
        return $this->_get(self::USE_CONFIG_MANAGE_STOCK);
    }

    /**
     * @return bool
     */
    public function getStockStatusChangedAuto()
    {
        return $this->_get(self::STOCK_STATUS_CHANGED_AUTO);
    }

    /**
     * @return bool
     */
    public function canUseConfigQtyIncrements()
    {
        return $this->_get(self::USE_CONFIG_QTY_INCREMENTS);
    }

    /**
     * @return int
     */
    public function getQtyIncrements()
    {
        return $this->_get(self::QTY_INCREMENTS);
    }

    /**
     * @return bool
     */
    public function canUseConfigEnableQtyInc()
    {
        return $this->_get(self::USE_CONFIG_ENABLE_QTY_INC);
    }

    /**
     * @return bool
     */
    public function getEnableQtyIncrements()
    {
        return $this->_get(self::ENABLE_QTY_INCREMENTS);
    }

    /**
     * @return bool
     */
    public function isDecimalDivided()
    {
        return $this->_get(self::IS_DECIMAL_DIVIDED);
    }
}
