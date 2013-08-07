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
 * GiftWrapping total calculator for invoice
 *
 */
class Magento_GiftWrapping_Model_Total_Invoice_Giftwrapping extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect gift wrapping totals
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Magento_GiftWrapping_Model_Total_Invoice_Giftwrapping
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
            if ($orderItem->getGwId() && $orderItem->getGwBasePrice()
                && $orderItem->getGwBasePrice() != $orderItem->getGwBasePriceInvoiced()) {
                $orderItem->setGwBasePriceInvoiced($orderItem->getGwBasePrice());
                $orderItem->setGwPriceInvoiced($orderItem->getGwPrice());
                $baseInvoiced += $orderItem->getGwBasePrice();
                $invoiced += $orderItem->getGwPrice();
            }
        }
        if ($invoiced > 0 || $baseInvoiced > 0) {
            $order->setGwItemsBasePriceInvoiced($order->getGwItemsBasePriceInvoiced() + $baseInvoiced);
            $order->setGwItemsPriceInvoiced($order->getGwItemsPriceInvoiced() + $invoiced);
            $invoice->setGwItemsBasePrice($baseInvoiced);
            $invoice->setGwItemsPrice($invoiced);
        }

        /**
         * Wrapping for order
         */
        if ($order->getGwId() && $order->getGwBasePrice()
            && $order->getGwBasePrice() != $order->getGwBasePriceInvoiced()) {
            $order->setGwBasePriceInvoiced($order->getGwBasePrice());
            $order->setGwPriceInvoiced($order->getGwPrice());
            $invoice->setGwBasePrice($order->getGwBasePrice());
            $invoice->setGwPrice($order->getGwPrice());
        }

        /**
         * Printed card
         */
        if ($order->getGwAddCard() && $order->getGwCardBasePrice()
            && $order->getGwCardBasePrice() != $order->getGwCardBasePriceInvoiced()) {
            $order->setGwCardBasePriceInvoiced($order->getGwCardBasePrice());
            $order->setGwCardPriceInvoiced($order->getGwCardPrice());
            $invoice->setGwCardBasePrice($order->getGwCardBasePrice());
            $invoice->setGwCardPrice($order->getGwCardPrice());
        }

        $invoice->setBaseGrandTotal(
            $invoice->getBaseGrandTotal()
            + $invoice->getGwItemsBasePrice()
            + $invoice->getGwBasePrice()
            + $invoice->getGwCardBasePrice()
        );
        $invoice->setGrandTotal(
            $invoice->getGrandTotal()
            + $invoice->getGwItemsPrice()
            + $invoice->getGwPrice()
            + $invoice->getGwCardPrice()
        );
        return $this;
    }
}
