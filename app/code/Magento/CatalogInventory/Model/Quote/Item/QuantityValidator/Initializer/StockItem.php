<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer;

/**
 * Class StockItem
 */
class StockItem
{
    /**
     * @var QtyProcessor
     */
    protected $qtyProcessor;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $typeConfig;

    /**
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig
     * @param QtyProcessor $qtyProcessor
     */
    public function __construct(
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig,
        QtyProcessor $qtyProcessor
    ) {
        $this->qtyProcessor = $qtyProcessor;
        $this->typeConfig = $typeConfig;
    }

    /**
     * Initialize stock item
     *
     * @param \Magento\CatalogInventory\Model\Stock\Item $stockItem
     * @param \Magento\Sales\Model\Quote\Item $quoteItem
     * @param int $qty
     *
     * @return \Magento\Framework\Object
     * @throws \Magento\Framework\Model\Exception
     */
    public function initialize(
        \Magento\CatalogInventory\Model\Stock\Item $stockItem,
        \Magento\Sales\Model\Quote\Item $quoteItem,
        $qty
    ) {
        $this->qtyProcessor->setItem($quoteItem);
        $rowQty = $this->qtyProcessor->getRowQty($qty);
        $qtyForCheck = $this->qtyProcessor->getQtyForCheck($qty);

        $productTypeCustomOption = $quoteItem->getProduct()->getCustomOption('product_type');
        if (!is_null($productTypeCustomOption)) {
            // Check if product related to current item is a part of product that represents product set
            if ($this->typeConfig->isProductSet($productTypeCustomOption->getValue())) {
                $stockItem->setIsChildItem(true);
            }
        }

        $stockItem->setProductName($quoteItem->getProduct()->getName());

        $stockItem->setProduct($quoteItem->getProduct());
        $result = $stockItem->checkQuoteItemQty($rowQty, $qtyForCheck, $qty);

        if ($stockItem->hasIsChildItem()) {
            $stockItem->unsIsChildItem();
        }

        if (!is_null($result->getItemIsQtyDecimal())) {
            $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
            }
        }

        /**
         * Just base (parent) item qty can be changed
         * qty of child products are declared just during add process
         * exception for updating also managed by product type
         */
        if ($result->getHasQtyOptionUpdate() && (!$quoteItem->getParentItem() ||
            $quoteItem->getParentItem()->getProduct()->getTypeInstance()->getForceChildItemQtyChanges(
                $quoteItem->getParentItem()->getProduct()
            )
        )
        ) {
            $quoteItem->setData('qty', $result->getOrigQty());
        }

        if (!is_null($result->getItemUseOldQty())) {
            $quoteItem->setUseOldQty($result->getItemUseOldQty());
        }

        if (!is_null($result->getMessage())) {
            $quoteItem->setMessage($result->getMessage());
        }

        if (!is_null($result->getItemBackorders())) {
            $quoteItem->setBackorders($result->getItemBackorders());
        }

        return $result;
    }
}
