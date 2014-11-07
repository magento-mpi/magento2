<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1\Data;

use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Stock status data object
 *
 * @codeCoverageIgnore
 */
class StockStatus extends AbstractExtensibleObject
{
    /**#@+
     * Stock status object data keys
     */
    const STOCK_STATUS = 'is_in_stock';

    const STOCK_QTY = 'qty';

    /**#@-*/

    /**
     * @return bool
     */
    public function getIsInStock()
    {
        return $this->_get(self::STOCK_STATUS);
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->_get(self::STOCK_QTY);
    }
}
