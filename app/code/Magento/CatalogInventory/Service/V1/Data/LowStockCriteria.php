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
 * Low stock criteria data object
 *
 * @codeCoverageIgnore
 */
class LowStockCriteria extends AbstractExtensibleObject
{
    /**#@+
     * Stock status object data keys
     */
    const QTY = 'qty';
    const PAGE_SIZE = 'page_size';
    const CURRENT_PAGE = 'current_page';
    /**#@-*/

    /**
     * @return float
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Get page size
     *
     * @return int|null
     */
    public function getPageSize()
    {
        return $this->_get(self::PAGE_SIZE);
    }

    /**
     * Get current page
     *
     * @return int|null
     */
    public function getCurrentPage()
    {
        return $this->_get(self::CURRENT_PAGE);
    }
}
