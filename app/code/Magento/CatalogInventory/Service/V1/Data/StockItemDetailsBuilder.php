<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1\Data;

use Magento\Framework\Api\ExtensibleObjectBuilder;

/**
 * Stock item details data builder
 *
 * @codeCoverageIgnore
 */
class StockItemDetailsBuilder extends ExtensibleObjectBuilder
{
    /**
     * @param int $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->_set(StockItemDetails::QTY, $qty);
    }

    /**
     * @param int $minQty
     * @return $this
     */
    public function setMinQty($minQty)
    {
        return $this->_set(StockItemDetails::MIN_QTY, $minQty);
    }

    /**
     * @param bool $isQtyDecimal
     * @return $this
     */
    public function setIsQtyDecimal($isQtyDecimal)
    {
        return $this->_set(StockItemDetails::IS_QTY_DECIMAL, $isQtyDecimal);
    }

    /**
     * @param bool $backorders
     * @return $this
     */
    public function setBackorders($backorders)
    {
        return $this->_set(StockItemDetails::BACKORDERS, $backorders);
    }

    /**
     * @param float $minSaleQty
     * @return $this
     */
    public function setMinSaleQty($minSaleQty)
    {
        return $this->_set(StockItemDetails::MIN_SALE_QTY, $minSaleQty);
    }

    /**
     * @param float $maxSaleQty
     * @return $this
     */
    public function setMaxSaleQty($maxSaleQty)
    {
        return $this->_set(StockItemDetails::MAX_SALE_QTY, $maxSaleQty);
    }

    /**
     * @param bool $isInStock
     * @return $this
     */
    public function setIsInStock($isInStock)
    {
        return $this->_set(StockItemDetails::IS_IN_STOCK, $isInStock);
    }

    /**
     * @param string $lowStockDate
     * @return $this
     */
    public function setLowStockDate($lowStockDate)
    {
        return $this->_set(StockItemDetails::LOW_STOCK_DATE, $lowStockDate);
    }

    /**
     * @param float $notifyStockQty
     * @return $this
     */
    public function setNotifyStockQty($notifyStockQty)
    {
        return $this->_set(StockItemDetails::NOTIFY_STOCK_QTY, $notifyStockQty);
    }

    /**
     * @param bool $manageStock
     * @return $this
     */
    public function setManageStock($manageStock)
    {
        return $this->_set(StockItemDetails::MANAGE_STOCK, $manageStock);
    }

    /**
     * @param bool $stockStatusChangedAuto
     * @return $this
     */
    public function setStockStatusChangedAuto($stockStatusChangedAuto)
    {
        return $this->_set(StockItemDetails::STOCK_STATUS_CHANGED_AUTO, $stockStatusChangedAuto);
    }

    /**
     * @param float $qtyIncrements
     * @return $this
     */
    public function setQtyIncrements($qtyIncrements)
    {
        return $this->_set(StockItemDetails::QTY_INCREMENTS, $qtyIncrements);
    }

    /**
     * @param bool $enableQtyIncrements
     * @return $this
     */
    public function setEnableQtyIncrements($enableQtyIncrements)
    {
        return $this->_set(StockItemDetails::ENABLE_QTY_INCREMENTS, $enableQtyIncrements);
    }

    /**
     * @param bool $isDecimalDivided
     * @return $this
     */
    public function setIsDecimalDivided($isDecimalDivided)
    {
        return $this->_set(StockItemDetails::IS_DECIMAL_DIVIDED, $isDecimalDivided);
    }
}
