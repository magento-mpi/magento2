<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product quote initializer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 */
namespace Magento\Sales\Model\AdminOrder\Product\Quote;

class Initializer
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
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Object $config
     * @return \Magento\Sales\Model\Quote\Item|string
     */
    public function init(
        \Magento\Sales\Model\Quote $quote,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Object $config
    ) {
        /** @var \Magento\CatalogInventory\Service\V1\Data\StockItem $stockItemDo */
        $stockItemDo = $this->stockItemService->getStockItem($product->getId());
        if ($stockItemDo->getStockId() && $stockItemDo->getIsQtyDecimal()) {
            $product->setIsQtyDecimal(1);
        } else {
            $config->setQty((int)$config->getQty());
        }

        $product->setCartQty($config->getQty());

        $item = $quote->addProductAdvanced(
            $product,
            $config,
            \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
        );

        return $item;
    }
}
