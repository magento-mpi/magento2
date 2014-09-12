<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model\Total\Invoice;

class Weee extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * Weee data
     *
     * @var \Magento\Weee\Helper\Data
     */
    protected $_weeeData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\Weee\Helper\Data $weeeData
     * @param array $data
     */
    public function __construct(\Magento\Weee\Helper\Data $weeeData, array $data = array())
    {
        $this->_weeeData = $weeeData;
        parent::__construct($data);
    }

    /**
     * Collect Weee amounts for the invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $store = $invoice->getStore();
        $order = $invoice->getOrder();

        $totalWeeeAmount = 0;
        $baseTotalWeeeAmount = 0;
        $totalWeeeAmountInclTax = 0;
        $baseTotalWeeeAmountInclTax = 0;

        $weeeTax = 0;
        $baseWeeeTax = 0;

        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            $orderItemQty = $orderItem->getQtyOrdered();

            if (!$orderItemQty || $orderItem->isDummy() || $item->getQty() <= 0) {
                continue;
            }

            $ratio = $item->getQty() / $orderItemQty;
            $weeeAmount = $invoice->roundPrice($orderItem->getWeeeTaxAppliedRowAmount() * $ratio);
            $baseWeeeAmount = $invoice->roundPrice($orderItem->getBaseWeeeTaxAppliedRowAmnt() * $ratio, 'base');

            $weeeAmountInclTax = $invoice->roundPrice(
                $this->_weeeData->getRowWeeeTaxInclTax($orderItem) * $ratio            );
            $baseWeeeAmountInclTax = $invoice->roundPrice(
                $this->_weeeData->getBaseRowWeeeTaxInclTax($orderItem) * $ratio,
                'base'
            );
            
            $item->setWeeeTaxAppliedRowAmount($weeeAmount);
            $item->setBaseWeeeTaxAppliedRowAmount($baseWeeeAmount);
            $newApplied = array();
            $applied = $this->_weeeData->getApplied($item);
            foreach ($applied as $one) {
                $one['base_row_amount'] = $baseWeeeAmount;
                $one['row_amount'] = $weeeAmount;
                $one['base_row_amount_incl_tax'] = $baseWeeeAmountInclTax;
                $one['row_amount_incl_tax'] = $weeeAmountInclTax;

                $newApplied[] = $one;
            }
            $this->_weeeData->setApplied($item, $newApplied);

            $item->setWeeeTaxRowDisposition($item->getWeeeTaxDisposition() * $item->getQty());
            $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxDisposition() * $item->getQty());

            $totalWeeeAmount += $weeeAmount;
            $baseTotalWeeeAmount += $baseWeeeAmount;
            
            $totalWeeeAmountInclTax += $weeeAmountInclTax;
            $baseTotalWeeeAmountInclTax += $baseWeeeAmountInclTax;
        }

        //Add tax applied to weee to tax amount
        $weeeTax = $totalWeeeAmountInclTax - $totalWeeeAmount;
        $baseWeeeTax = $baseTotalWeeeAmountInclTax - $baseTotalWeeeAmount;
        $allowedTax = $order->getTaxAmount() - $order->getTaxInvoiced() - $invoice->getTaxAmount();
        $allowedBaseTax = $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced() - $invoice->getBaseTaxAmount();
        $weeeTax = min($weeeTax, $allowedTax);
        $baseWeeeTax = min($baseWeeeTax, $allowedBaseTax);
        $invoice->setTaxAmount($invoice->getTaxAmount() + $weeeTax);
        $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $baseWeeeTax);

        // Add FPT to subtotal and grand total
        if ($this->_weeeData->includeInSubtotal($store)) {
            $order = $invoice->getOrder();
            $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced() - $invoice->getSubtotal();
            $allowedBaseSubtotal = $order->getBaseSubtotal() -
                $order->getBaseSubtotalInvoiced() -
                $invoice->getBaseSubtotal();
            $totalWeeeAmount = min($allowedSubtotal, $totalWeeeAmount);
            $baseTotalWeeeAmount = min($allowedBaseSubtotal, $baseTotalWeeeAmount);

            $invoice->setSubtotal($invoice->getSubtotal() + $totalWeeeAmount);
            $invoice->setBaseSubtotal($invoice->getBaseSubtotal() + $baseTotalWeeeAmount);
        }

        if (!$invoice->isLast()) {
            // need to add the Weee amounts including all their taxes
            $invoice->setSubtotalInclTax($invoice->getSubtotalInclTax() + $totalWeeeAmountInclTax);
            $invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax() + $baseTotalWeeeAmountInclTax);
        } else {
            // since the Subtotal Incl Tax line will already have the taxes on Weee, just add the non-taxable amounts
            $invoice->setSubtotalInclTax($invoice->getSubtotalInclTax() + $totalWeeeAmount);
            $invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax() + $baseTotalWeeeAmount);
        }

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalWeeeAmount + $weeeTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalWeeeAmount + $baseWeeeTax);

        return $this;
    }
}
