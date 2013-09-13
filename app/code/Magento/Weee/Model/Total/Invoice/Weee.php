<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Weee_Model_Total_Invoice_Weee extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Weee data
     *
     * @var Magento_Weee_Helper_Data
     */
    protected $_weeeData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Weee_Helper_Data $weeeData
     * @param array $data
     */
    public function __construct(
        Magento_Weee_Helper_Data $weeeData,
        array $data = array()
    ) {
        $this->_weeeData = $weeeData;
        parent::__construct($data);
    }

    /**
     * Weee tax collector
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Magento_Weee_Model_Total_Invoice_Weee
     */
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
    {
        $store = $invoice->getStore();

        $totalTax = 0;
        $baseTotalTax = 0;

        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            $orderItemQty = $orderItem->getQtyOrdered();

            if (!$orderItemQty || $orderItem->isDummy()) {
                continue;
            }

            $weeeTaxAmount = $item->getWeeeTaxAppliedAmount() * $item->getQty();
            $baseWeeeTaxAmount = $item->getBaseWeeeTaxAppliedAmount() * $item->getQty();

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
        }

        /*
         * Add FPT to totals
         * Notice that we check restriction on allowed tax, because
         * a) for last invoice we don't need to collect FPT - it is automatically collected by subtotal/tax collector,
         * that adds whole remaining (not invoiced) subtotal/tax value, so fpt is automatically included into it
         * b) FPT tax is included into order subtotal/tax value, so after multiple invoices with partial item quantities
         * it can happen that other collector will take some FPT value from shared subtotal/tax order value
         */
        $order = $invoice->getOrder();
        if ($this->_weeeData->includeInSubtotal($store)) {
            $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced() - $invoice->getSubtotal();
            $allowedBaseSubtotal = $order->getBaseSubtotal() - $order->getBaseSubtotalInvoiced()
                - $invoice->getBaseSubtotal();
            $totalTax = min($allowedSubtotal, $totalTax);
            $baseTotalTax = min($allowedBaseSubtotal, $baseTotalTax);

            $invoice->setSubtotal($invoice->getSubtotal() + $totalTax);
            $invoice->setBaseSubtotal($invoice->getBaseSubtotal() + $baseTotalTax);
        } else {
            $allowedTax = $order->getTaxAmount() - $order->getTaxInvoiced() - $invoice->getTaxAmount();
            $allowedBaseTax = $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced() - $invoice->getBaseTaxAmount();
            $totalTax = min($allowedTax, $totalTax);
            $baseTotalTax = min($allowedBaseTax, $baseTotalTax);

            $invoice->setTaxAmount($invoice->getTaxAmount() + $totalTax);
            $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $baseTotalTax);
        }

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
