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
 * Stock item details data object
 */
class StockItemDetails extends AbstractObject
{
    /**#@+
     * Stock item object data keys
     */
    const QTY = 'qty';

    const MIN_QTY = 'min_qty';

    const IS_QTY_DECIMAL = 'is_qty_decimal';

    const BACKORDERS = 'backorders';

    const MIN_SALE_QTY = 'min_sale_qty';

    const MAX_SALE_QTY = 'max_sale_qty';

    const IS_IN_STOCK = 'is_in_stock';

    const LOW_STOCK_DATE = 'low_stock_date';

    const NOTIFY_STOCK_QTY = 'notify_stock_qty';

    const MANAGE_STOCK = 'manage_stock';

    const STOCK_STATUS_CHANGED_AUTO = 'stock_status_changed_auto';

    const QTY_INCREMENTS = 'qty_increments';

    const ENABLE_QTY_INCREMENTS = 'enable_qty_increments';

    const IS_DECIMAL_DIVIDED = 'is_decimal_divided';
    /**#@-*/

    /**
     * @return float
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * @return float
     */
    public function getMinQty()
    {
        return $this->_get(self::MIN_QTY);
    }

    /**
     * @return bool
     */
    public function getIsQtyDecimal()
    {
        return $this->_get(self::IS_QTY_DECIMAL);
    }

    /**
     * @return bool
     */
    public function isBackorders()
    {
        return $this->_get(self::BACKORDERS);
    }

    /**
     * @return float
     */
    public function getMinSaleQty()
    {
        return $this->_get(self::MIN_SALE_QTY);
    }

    /**
     * @return float
     */
    public function getMaxSaleQty()
    {
        return $this->_get(self::MAX_SALE_QTY);
    }

    /**
     * @return bool
     */
    public function getIsInStock()
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
     * @return float
     */
    public function getNotifyStockQty()
    {
        return $this->_get(self::NOTIFY_STOCK_QTY);
    }

    /**
     * @return bool
     */
    public function isManageStock()
    {
        return $this->_get(self::MANAGE_STOCK);
    }

    /**
     * @return bool
     */
    public function isStockStatusChangedAuto()
    {
        return $this->_get(self::STOCK_STATUS_CHANGED_AUTO);
    }

    /**
     * @return float
     */
    public function getQtyIncrements()
    {
        return $this->_get(self::QTY_INCREMENTS);
    }

    /**
     * @return bool
     */
    public function isEnableQtyIncrements()
    {
        return $this->_get(self::ENABLE_QTY_INCREMENTS);
    }

    /**
     * @return bool
     */
    public function getIsDecimalDivided()
    {
        return $this->_get(self::IS_DECIMAL_DIVIDED);
    }
}
