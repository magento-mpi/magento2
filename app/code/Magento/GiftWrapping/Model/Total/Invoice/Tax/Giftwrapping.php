<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftWrapping total tax calculator for invoice
 *
 */
class Magento_GiftWrapping_Model_Total_Invoice_Tax_Giftwrapping extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect gift wrapping tax totals
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Magento_GiftWrapping_Model_Total_Invoice_Tax_Giftwrapping
     */
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();

        /**
         * Wrapping for items
         */
        $invoiced = 0;
        $baseInvoiced = 0;
        foreach ($invoice->getAllItems() as $invoiceItem) {
            if (!$invoiceItem->getQty() || $invoiceItem->getQty() == 0) {
                continue;
            }
            $orderItem = $invoiceItem->getOrderItem();
            if ($orderItem->getGwId() && $orderItem->getGwBaseTaxAmount()
                && $orderItem->getGwBaseTaxAmount() != $orderItem->getGwBaseTaxAmountInvoiced()) {
                $orderItem->setGwBaseTaxAmountInvoiced($orderItem->getGwBaseTaxAmount());
                $orderItem->setGwTaxAmountInvoiced($orderItem->getGwTaxAmount());
                $baseInvoiced += $orderItem->getGwBaseTaxAmount();
                $invoiced += $orderItem->getGwTaxAmount();
            }
        }
        if ($invoiced > 0 || $baseInvoiced > 0) {
            $order->setGwItemsBaseTaxInvoiced($order->getGwItemsBaseTaxInvoiced() + $baseInvoiced);
            $order->setGwItemsTaxInvoiced($order->getGwItemsTaxInvoiced() + $invoiced);
            $invoice->setGwItemsBaseTaxAmount($baseInvoiced);
            $invoice->setGwItemsTaxAmount($invoiced);
        }

        /**
         * Wrapping for order
         */
        if ($order->getGwId() && $order->getGwBaseTaxAmount()
            && $order->getGwBaseTaxAmount() != $order->getGwBaseTaxAmountInvoiced()) {
            $order->setGwBaseTaxAmountInvoiced($order->getGwBaseTaxAmount());
            $order->setGwTaxAmountInvoiced($order->getGwTaxAmount());
            $invoice->setGwBaseTaxAmount($order->getGwBaseTaxAmount());
            $invoice->setGwTaxAmount($order->getGwTaxAmount());
        }

        /**
         * Printed card
         */
        if ($order->getGwAddCard() && $order->getGwCardBaseTaxAmount()
            && $order->getGwCardBaseTaxAmount() != $order->getGwCardBaseTaxInvoiced()) {
            $order->setGwCardBaseTaxInvoiced($order->getGwCardBaseTaxAmount());
            $order->setGwCardTaxInvoiced($order->getGwCardTaxAmount());
            $invoice->setGwCardBaseTaxAmount($order->getGwCardBaseTaxAmount());
            $invoice->setGwCardTaxAmount($order->getGwCardTaxAmount());
        }

        if (!$invoice->isLast()) {
            $baseTaxAmount = $invoice->getGwItemsBaseTaxAmount()
                + $invoice->getGwBaseTaxAmount()
                + $invoice->getGwCardBaseTaxAmount();
            $taxAmount = $invoice->getGwItemsTaxAmount()
                + $invoice->getGwTaxAmount()
                + $invoice->getGwCardTaxAmount();
            $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $baseTaxAmount);
            $invoice->setTaxAmount($invoice->getTaxAmount() + $taxAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTaxAmount);
            $invoice->setGrandTotal($invoice->getGrandTotal() + $taxAmount);
        }

        return $this;
    }
}
