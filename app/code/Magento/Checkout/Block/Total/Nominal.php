<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal total rendered
 *
 * Each item is rendered as separate total with its details
 */
namespace Magento\Checkout\Block\Total;

class Nominal extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * Custom template
     *
     * @var string
     */
    protected $_template = 'total/nominal.phtml';

    /**
     * Getter for a quote item name
     *
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $quoteItem
     * @return string
     */
    public function getItemName(\Magento\Sales\Model\Quote\Item\AbstractItem $quoteItem)
    {
        return $quoteItem->getName();
    }

    /**
     * Getter for a quote item row total
     *
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $quoteItem
     * @return float
     */
    public function getItemRowTotal(\Magento\Sales\Model\Quote\Item\AbstractItem $quoteItem)
    {
        return $quoteItem->getNominalRowTotal();
    }

    /**
     * Getter for nominal total item details
     *
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $quoteItem
     * @return array
     */
    public function getTotalItemDetails(\Magento\Sales\Model\Quote\Item\AbstractItem $quoteItem)
    {
        return $quoteItem->getNominalTotalDetails();
    }

    /**
     * Getter for details row label
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getItemDetailsRowLabel(\Magento\Object $row)
    {
        return $row->getLabel();
    }

    /**
     * Getter for details row amount
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getItemDetailsRowAmount(\Magento\Object $row)
    {
        return $row->getAmount();
    }

    /**
     * Getter for details row compounded state
     *
     * @param \Magento\Object $row
     * @return bool
     */
    public function getItemDetailsRowIsCompounded(\Magento\Object $row)
    {
        return $row->getIsCompounded();
    }

    /**
     * Format an amount without container
     *
     * @param float $amount
     * @return string
     */
    public function formatPrice($amount)
    {
        return $this->_store->formatPrice($amount, false);
    }

    /**
     * Import total data into the block, if there are items
     *
     * @return string
     */
    protected function _toHtml()
    {
        $total = $this->getTotal();
        $items = $total->getItems();
        if ($items) {
            foreach ($total->getData() as $key => $value) {
                $this->setData("total_{$key}", $value);
            }
            return parent::_toHtml();
        }
        return '';
    }
}
