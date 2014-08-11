<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Block\Item\Price\OrderItem;

use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\CreditMemo\Item as CreditMemoItem;

/**
 * Order item price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Weee\Block\Item\Price\Renderer
{
    /**
     * Format price using order currency
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        $item = $this->getItem();
        if ($item instanceof OrderItem) {
            return $item->getOrder()->formatPrice($price);
        } else {
            return $item->getOrderItem()->getOrder()->formatPrice($price);
        }
    }

    /**
     * Return the total amount minus discount
     *
     * @param OrderItem|InvoiceItem|CreditMemoItem $_item
     * @return mixed
     */
    public function getTotalAmount($_item)
    {
        $totalAmount = $_item->getRowTotal()
            - $_item->getDiscountAmount()
            + $_item->getTaxAmount()
            + $_item->getHiddenTaxAmount()
            + $this->weeeHelper->getRowWeeeTaxInclTax($_item);

        return $totalAmount;
    }
}
