<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Product\CopyConstructor;

class CatalogInventory implements \Magento\Catalog\Model\Product\CopyConstructorInterface
{
    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService
     */
    protected $stockItemService;

    /**
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     */
    public function __construct(
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
    ) {
        $this->stockItemService = $stockItemService;
    }

    /**
     * Copy product inventory data (used for product duplicate functionality)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $duplicate
     * @return void
     */
    public function build(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Product $duplicate)
    {
        $stockData = [
            'use_config_min_qty' => 1,
            'use_config_min_sale_qty' => 1,
            'use_config_max_sale_qty' => 1,
            'use_config_backorders' => 1,
            'use_config_notify_stock_qty' => 1
        ];
        /** @var \Magento\CatalogInventory\Model\Stock\Item $currentStockItem */
        $currentStockItemDo = $this->stockItemService->getStockItem($product->getId());
        if ($currentStockItemDo->getStockId()) {
            $stockData += [
                'use_config_enable_qty_inc' => $currentStockItemDo->isUseConfigEnableQtyInc(),
                'enable_qty_increments' => $currentStockItemDo->isEnableQtyIncrements(),
                'use_config_qty_increments' => $currentStockItemDo->isUseConfigQtyIncrements(),
                'qty_increments' => $currentStockItemDo->getQtyIncrements(),
            ];
        }
        $duplicate->setStockData($stockData);
    }
}
