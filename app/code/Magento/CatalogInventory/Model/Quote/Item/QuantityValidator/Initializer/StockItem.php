<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;

class StockItem
{
    /**
     * @var QuoteItemQtyList
     */
    protected $quoteItemQtyList;

    /**
     * @var ConfigInterface
     */
    protected $typeConfig;

    /**
     * @var StockStateInterface
     */
    protected $stockState;

    /**
     * @param ConfigInterface $typeConfig
     * @param QuoteItemQtyList $quoteItemQtyList
     * @param StockStateInterface $stockState
     */
    public function __construct(
        ConfigInterface $typeConfig,
        QuoteItemQtyList $quoteItemQtyList,
        StockStateInterface $stockState
    ) {
        $this->quoteItemQtyList = $quoteItemQtyList;
        $this->typeConfig = $typeConfig;
        $this->stockState = $stockState;
    }

    /**
     * Initialize stock item
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param \Magento\Sales\Model\Quote\Item $quoteItem
     * @param int $qty
     *
     * @return \Magento\Framework\Object
     * @throws \Magento\Framework\Model\Exception
     */
    public function initialize(
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        \Magento\Sales\Model\Quote\Item $quoteItem,
        $qty
    ) {
        /**
         * When we work with subitem
         */
        if ($quoteItem->getParentItem()) {
            $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
            /**
             * we are using 0 because original qty was processed
             */
            $qtyForCheck = $this->quoteItemQtyList
                ->getQty($quoteItem->getProduct()->getId(), $quoteItem->getId(), $quoteItem->getQuoteId(), 0);
        } else {
            $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
            $rowQty = $qty;
            $qtyForCheck = $this->quoteItemQtyList->getQty(
                $quoteItem->getProduct()->getId(),
                $quoteItem->getId(),
                $quoteItem->getQuoteId(),
                $increaseQty
            );
        }

        $productTypeCustomOption = $quoteItem->getProduct()->getCustomOption('product_type');
        if (!is_null($productTypeCustomOption)) {
            // Check if product related to current item is a part of product that represents product set
            if ($this->typeConfig->isProductSet($productTypeCustomOption->getValue())) {
                $stockItem->setIsChildItem(true);
            }
        }

        $stockItem->setProductName($quoteItem->getProduct()->getName());

        $result = $this->stockState->checkQuoteItemQty(
            $quoteItem->getProduct()->getId(),
            $rowQty,
            $qtyForCheck,
            $qty,
            $quoteItem->getProduct()->getStore()->getWebsiteId()
        );

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
