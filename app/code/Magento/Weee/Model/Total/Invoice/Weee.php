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

        $totalTax = 0;
        $baseTotalTax = 0;
        $weeeInclTax = 0;
        $baseWeeeInclTax = 0;

        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            $orderItemQty = $orderItem->getQtyOrdered();

            if (!$orderItemQty || $orderItem->isDummy()) {
                continue;
            }

            $weeeTaxAmount = $item->getWeeeTaxAppliedAmount() * $item->getQty();
            $baseWeeeTaxAmount = $item->getBaseWeeeTaxAppliedAmount() * $item->getQty();

            $weeeTaxAmountInclTax = $this->_weeeData->getWeeeTaxInclTax($item) * $item->getQty();
            $baseWeeeTaxAmountInclTax = $this->_weeeData->getBaseWeeeTaxInclTax($item) * $item->getQty();
            
            $item->setWeeeTaxAppliedRowAmount($weeeTaxAmount);
            $item->setBaseWeeeTaxAppliedRowAmount($baseWeeeTaxAmount);
            $newApplied = array();
            $applied = $this->_weeeData->getApplied($item);
            foreach ($applied as $one) {
                $one['base_row_amount'] = $one['base_amount'] * $item->getQty();
                $one['row_amount'] = $one['amount'] * $item->getQty();
                $one['base_row_amount_incl_tax'] = $one['base_amount_incl_tax'] * $item->getQty();
                $one['row_amount_incl_tax'] = $one['amount_incl_tax'] * $item->getQty();

                $newApplied[] = $one;
            }
            $this->_weeeData->setApplied($item, $newApplied);

            $item->setWeeeTaxRowDisposition($item->getWeeeTaxDisposition() * $item->getQty());
            $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxDisposition() * $item->getQty());

            $totalTax += $weeeTaxAmount;
            $baseTotalTax += $baseWeeeTaxAmount;
            
            $weeeInclTax += $weeeTaxAmountInclTax;
            $baseWeeeInclTax += $baseWeeeTaxAmountInclTax;
        }

        // Add FPT to subtotal and grand total
        if ($this->_weeeData->includeInSubtotal($store)) {
            $order = $invoice->getOrder();
            $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced() - $invoice->getSubtotal();
            $allowedBaseSubtotal = $order->getBaseSubtotal() -
                $order->getBaseSubtotalInvoiced() -
                $invoice->getBaseSubtotal();
            $totalTax = min($allowedSubtotal, $totalTax);
            $baseTotalTax = min($allowedBaseSubtotal, $baseTotalTax);

            $invoice->setSubtotal($invoice->getSubtotal() + $totalTax);
            $invoice->setBaseSubtotal($invoice->getBaseSubtotal() + $baseTotalTax);
        }

        $useWeeeInclTax = true;
        if ($this->_weeeData->isTaxIncluded($store) && $invoice->isLast()) {
            $useWeeeInclTax = false;
        }
        if ($useWeeeInclTax) {
            // need to add the Weee amounts including all their taxes
            $invoice->setSubtotalInclTax($invoice->getSubtotalInclTax() + $weeeInclTax);
            $invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax() + $baseWeeeInclTax);
        } else {
            // since the Subtotal Incl Tax line will already have the taxes on Weee, just add the non-taxable amounts
            $invoice->setSubtotalInclTax($invoice->getSubtotalInclTax() + $totalTax);
            $invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax() + $baseTotalTax);
        }

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
