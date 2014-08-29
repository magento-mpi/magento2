<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1\Data;

/**
 * Low stock criteria builder
 *
 * @codeCoverageIgnore
 */
class LowStockCriteriaBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * Set page size
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->_set(LowStockCriteria::QTY, $qty);
    }

    /**
     * Set page size
     *
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        return $this->_set(LowStockCriteria::PAGE_SIZE, $pageSize);
    }

    /**
     * Set current page
     *
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        return $this->_set(LowStockCriteria::CURRENT_PAGE, $currentPage);
    }
}
