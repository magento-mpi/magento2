<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;

class Option
{
    /**
     * @var QuoteItemQtyList
     */
    protected $quoteItemQtyList;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory
     */
    protected $stockItemFactory;

    /**
     * @param QuoteItemQtyList $quoteItemQtyList
     * @param \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory
     */
    public function __construct(
        QuoteItemQtyList $quoteItemQtyList,
        \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory
    ) {
        $this->quoteItemQtyList = $quoteItemQtyList;
        $this->stockItemFactory = $stockItemFactory;
    }

    /**
     * Initialize item option
     *
     * @param \Magento\Sales\Model\Quote\Item\Option $option
     * @param \Magento\Sales\Model\Quote\Item $quoteItem
     * @param int $qty
     *
     * @return \Magento\Framework\Object
     *
     * @throws \Magento\Framework\Model\Exception
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
        $stockItem = $this->stockItemFactory->create()->loadByProduct($option->getProduct());

        if (!$stockItem instanceof \Magento\CatalogInventory\Model\Stock\Item) {
            throw new \Magento\Framework\Model\Exception(__('The stock item for Product in option is not valid.'));
        }

        /**
         * define that stock item is child for composite product
         */
        $stockItem->setIsChildItem(true);
        /**
         * don't check qty increments value for option product
         */
        $stockItem->setSuppressCheckQtyIncrements(true);

        $qtyForCheck = $this->quoteItemQtyList->getQty(
            $option->getProduct()->getId(),
            $quoteItem->getId(),
            $quoteItem->getQuoteId(),
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
}
