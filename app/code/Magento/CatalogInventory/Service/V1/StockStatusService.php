<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;


use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Model\Stock\Status;

/**
 * Service related to Product Stock Status
 */
class StockStatusService implements StockStatusServiceInterface
{
    /**
     * @var Status
     */
    protected $stockStatus;

    /**
     * @param Status $stockStatus
     */
    public function __construct(Status $stockStatus)
    {
        $this->stockStatus = $stockStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductStockStatus($productIds, $websiteId, $stockId = Stock::DEFAULT_STOCK_ID)
    {
        return $this->stockStatus->getProductStockStatus($productIds, $websiteId, $stockId);
    }
}
