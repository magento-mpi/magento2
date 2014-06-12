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
    private $stockStatus;

    /**
     * @var \Magento\Store\Model\Resolver\Website
     */
    private $scopeResolver;

    /**
     * @var \Magento\Catalog\Service\V1\Product\Link\ProductLoader
     */
    private $productLoader;

    /**
     * @var StockItemService
     */
    private $stockItemService;

    /**
     * @var Data\StockStatusBuilder
     */
    private $stockStatusBuilder;

    /**
     * @param Status $stockStatus
     * @param StockItemService $stockItemService
     * @param \Magento\Catalog\Service\V1\Product\Link\ProductLoader $productLoader
     * @param \Magento\Store\Model\Resolver\Website $scopeResolver
     * @param Data\StockStatusBuilder $stockStatusBuilder
     */
    public function __construct(
        Status $stockStatus,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        \Magento\Catalog\Service\V1\Product\Link\ProductLoader $productLoader,
        \Magento\Store\Model\Resolver\Website $scopeResolver,
        \Magento\CatalogInventory\Service\V1\Data\StockStatusBuilder $stockStatusBuilder
    ) {
        $this->stockStatus = $stockStatus;
        $this->stockItemService = $stockItemService;
        $this->productLoader = $productLoader;
        $this->scopeResolver = $scopeResolver;
        $this->stockStatusBuilder = $stockStatusBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductStockStatus($productIds, $websiteId, $stockId = Stock::DEFAULT_STOCK_ID)
    {
        return $this->stockStatus->getProductStockStatus($productIds, $websiteId, $stockId);
    }

    /**
     * Assign Stock Status to Product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $stockId
     * @param int $stockStatus
     * @return \Magento\CatalogInventory\Service\V1\StockStatusService
     */
    public function assignProduct(
        \Magento\Catalog\Model\Product $product,
        $stockId = Stock::DEFAULT_STOCK_ID,
        $stockStatus = null
    ) {
        $this->stockStatus->assignProduct($product, $stockId, $stockStatus);
        return $this;
    }

    /**
     * {inheritdoc}
     */
    public function getProductStockStatusBySku($sku)
    {
        $product = $this->productLoader->load($sku);

        $data = $this->stockStatus->getProductStockStatus(
            [$product->getId()],
            $this->scopeResolver->getScope()->getId()
        );
        $stockStatus = (bool)$data[1];

        $result = [
            Data\StockStatus::STOCK_STATUS => $stockStatus,
            Data\StockStatus::STOCK_QTY => $this->stockItemService->getStockQty($product->getId())
        ];

        $this->stockStatusBuilder->populateWithArray($result);

        return $this->stockStatusBuilder->create();
    }
}
