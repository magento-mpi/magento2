<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\Sales\Model\Quote\Item;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;

/**
 * Class QtyProcessor
 */
class QtyProcessor
{
    /**
     * @var QuoteItemQtyList
     */
    protected $quoteItemQtyList;

    /**
     * @param QuoteItemQtyList $quoteItemQtyList
     */
    public function __construct(QuoteItemQtyList $quoteItemQtyList)
    {
        $this->quoteItemQtyList = $quoteItemQtyList;
    }

    /**
     * @var Item
     */
    protected $item;

    /**
     * @param Item $quoteItem
     * @return $this
     */
    public function setItem(Item $quoteItem)
    {
        $this->item = $quoteItem;
        return $this;
    }

    /**
     * @param float $qty
     * @return float|int
     */
    public function getRowQty($qty)
    {
        $rowQty = $qty;
        if ($this->item->getParentItem()) {
            $rowQty = $this->item->getParentItem()->getQty() * $qty;
        }
        return $rowQty;
    }

    /**
     * @param int $qty
     * @return int
     */
    public function getQtyForCheck($qty)
    {
        if (!$this->item->getParentItem()) {
            $increaseQty = $this->item->getQtyToAdd() ? $this->item->getQtyToAdd() : $qty;
            return $this->quoteItemQtyList->getQty(
                $this->item->getProduct()->getId(),
                $this->item->getId(),
                $this->item->getQuoteId(),
                $increaseQty
            );
        }
        return $this->quoteItemQtyList->getQty(
            $this->item->getProduct()->getId(),
            $this->item->getId(),
            $this->item->getQuoteId(),
            0
        );
    }
}
