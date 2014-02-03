<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer;

class StockItem 
{
    /**
     * Product qty's checked
     * data is valid if you check quote item qty and use singleton instance
     *
     * @var array
     */
    protected $_checkedQuoteItems = array();

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $typeConfig;

    /**
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig
     */
    public function __construct(\Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig)
    {
        $this->typeConfig = $typeConfig;
    }

    /**
     * Initialize stock item
     *
     * @param \Magento\CatalogInventory\Model\Stock\Item $stockItem
     * @param \Magento\Sales\Model\Quote\Item $quoteItem
     * @param int $qty
     *
     * @return \Magento\Object
     * @throws \Magento\Core\Exception
     */
    public function initialize(
        \Magento\CatalogInventory\Model\Stock\Item $stockItem,
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
            $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                $quoteItem->getProduct()->getId(),
                $quoteItem->getId(),
                0
            );
        } else {
            $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
            $rowQty = $qty;
            $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                $quoteItem->getProduct()->getId(),
                $quoteItem->getId(),
                $increaseQty
            );
        }

        $productTypeCustomOption = $quoteItem->getProduct()->getCustomOption('product_type');
        if (!is_null($productTypeCustomOption)) {
            // Check if product related to current item is a part of product that represents product set
            if ($this->typeConfig->isProductSet($productTypeCustomOption->getValue())) {
                $stockItem->setProductName($quoteItem->getProduct()->getName());
                $stockItem->setIsChildItem(true);
            }
        }

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
        if ($result->getHasQtyOptionUpdate()
            && (!$quoteItem->getParentItem()
                || $quoteItem->getParentItem()->getProduct()->getTypeInstance()
                    ->getForceChildItemQtyChanges($quoteItem->getParentItem()->getProduct())
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

    /**
     * Get product qty includes information from all quote items
     * Need be used only in sungleton mode
     *
     * @param int   $productId
     * @param int   $quoteItemId
     * @param float $itemQty
     *
     * @return int
     */
    protected function _getQuoteItemQtyForCheck($productId, $quoteItemId, $itemQty)
    {
        $qty = $itemQty;
        if (isset($this->_checkedQuoteItems[$productId]['qty']) &&
            !in_array($quoteItemId, $this->_checkedQuoteItems[$productId]['items'])) {
            $qty += $this->_checkedQuoteItems[$productId]['qty'];
        }

        $this->_checkedQuoteItems[$productId]['qty'] = $qty;
        $this->_checkedQuoteItems[$productId]['items'][] = $quoteItemId;

        return $qty;
    }
} 
