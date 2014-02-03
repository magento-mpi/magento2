<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer;

class Option
{
    /**
     * Product qty's checked
     * data is valid if you check quote item qty and use singleton instance
     *
     * @var array
     */
    protected $_checkedQuoteItems = array();


    /**
     * Initialize item option
     *
     * @param \Magento\Sales\Model\Quote\Item\Option $option
     * @param \Magento\Sales\Model\Quote\Item $quoteItem
     * @param int $qty
     *
     * @return \Magento\Object
     *
     * @throws \Magento\Core\Exception
     */
    public function initialize(
        \Magento\Sales\Model\Quote\Item\Option $option,
        \Magento\Sales\Model\Quote\Item $quoteItem,
        $qty
    ) {
        $optionValue = $option->getValue();
        $optionQty = $qty * $optionValue;
        $increaseOptionQty = ($quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty) * $optionValue;

        /* @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
        $stockItem = $option->getProduct()->getStockItem();

        if (!$stockItem instanceof \Magento\CatalogInventory\Model\Stock\Item) {
            throw new \Magento\Core\Exception(__('The stock item for Product in option is not valid.'));
        }

        /**
         * define that stock item is child for composite product
         */
        $stockItem->setIsChildItem(true);
        /**
         * don't check qty increments value for option product
         */
        $stockItem->setSuppressCheckQtyIncrements(true);

        $qtyForCheck = $this->_getQuoteItemQtyForCheck(
            $option->getProduct()->getId(),
            $quoteItem->getId(),
            $increaseOptionQty
        );

        $result = $stockItem->checkQuoteItemQty($optionQty, $qtyForCheck, $optionValue);

        if (!is_null($result->getItemIsQtyDecimal())) {
            $option->setIsQtyDecimal($result->getItemIsQtyDecimal());
        }

        if ($result->getHasQtyOptionUpdate()) {
            $option->setHasQtyOptionUpdate(true);
            $quoteItem->updateQtyOption($option, $result->getOrigQty());
            $option->setValue($result->getOrigQty());
            /**
             * if option's qty was updates we also need to update quote item qty
             */
            $quoteItem->setData('qty', intval($qty));
        }
        if (!is_null($result->getMessage())) {
            $option->setMessage($result->getMessage());
            $quoteItem->setMessage($result->getMessage());
        }
        if (!is_null($result->getItemBackorders())) {
            $option->setBackorders($result->getItemBackorders());
        }

        $stockItem->unsIsChildItem();

        return $result;
    }

    /**
     * Get product qty includes information from all quote items
     * Need be used only in sungleton mode
     *
     * @param int   $productId
     * @param int   $quoteItemId
     * @param float $itemQty
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
