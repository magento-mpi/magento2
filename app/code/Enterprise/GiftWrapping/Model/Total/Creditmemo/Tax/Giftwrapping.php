<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftWrapping total tax calculator for creditmemo
 *
 */
class Enterprise_GiftWrapping_Model_Total_Creditmemo_Tax_Giftwrapping extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect gift wrapping tax totals
     *
     * @param   Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return  Enterprise_GiftWrapping_Model_Total_Creditmemo_Tax_Giftwrapping
     */
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();

        /**
         * Wrapping for items
         */
        $refunded = 0;
        $baseRefunded = 0;
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            if (!$creditmemoItem->getQty() || $creditmemoItem->getQty() == 0) {
                continue;
            }
            $orderItem = $creditmemoItem->getOrderItem();
            if ($orderItem->getGwId() && $orderItem->getGwBaseTaxAmountInvoiced()
                && $orderItem->getGwBaseTaxAmountInvoiced() != $orderItem->getGwBaseTaxAmountRefunded()) {
                $orderItem->setGwBaseTaxAmountRefunded($orderItem->getGwBaseTaxAmountInvoiced());
                $orderItem->setGwTaxAmountRefunded($orderItem->getGwTaxAmountInvoiced());
                $baseRefunded += $orderItem->getGwBaseTaxAmountInvoiced();
                $refunded += $orderItem->getGwTaxAmountInvoiced();
            }
        }
        if ($refunded > 0 || $baseRefunded > 0) {
            $order->setGwItemsBaseTaxRefunded($order->getGwItemsBaseTaxRefunded() + $baseRefunded);
            $order->setGwItemsTaxRefunded($order->getGwItemsTaxRefunded() + $refunded);
            $creditmemo->setGwItemsBaseTaxAmount($baseRefunded);
            $creditmemo->setGwItemsTaxAmount($refunded);
        }

        /**
         * Wrapping for order
         */
        if ($order->getGwId() && $order->getGwBaseTaxAmountInvoiced()
            && $order->getGwBaseTaxAmountInvoiced() != $order->getGwBaseTaxAmountRefunded()) {
            $order->setGwBaseTaxAmountRefunded($order->getGwBaseTaxAmountInvoiced());
            $order->setGwTaxAmountRefunded($order->getGwTaxAmountInvoiced());
            $creditmemo->setGwBaseTaxAmount($order->getGwBaseTaxAmountInvoiced());
            $creditmemo->setGwTaxAmount($order->getGwTaxAmountInvoiced());
        }

        /**
         * Printed card
         */
        if ($order->getGwAddCard() && $order->getGwCardBaseTaxInvoiced()
            && $order->getGwCardBaseTaxInvoiced() != $order->getGwCardBaseTaxRefunded()) {
            $order->setGwCardBaseTaxRefunded($order->getGwCardBaseTaxInvoiced());
            $order->setGwCardTaxRefunded($order->getGwCardTaxInvoiced());
            $creditmemo->setGwCardBaseTaxAmount($order->getGwCardBaseTaxInvoiced());
            $creditmemo->setGwCardTaxAmount($order->getGwCardTaxInvoiced());
        }

        $baseTaxAmount = $creditmemo->getGwItemsBaseTaxAmount()
            + $creditmemo->getGwBaseTaxAmount()
            + $creditmemo->getGwCardBaseTaxAmount();
        $taxAmount = $creditmemo->getGwItemsTaxAmount()
            + $creditmemo->getGwTaxAmount()
            + $creditmemo->getGwCardTaxAmount();
        $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTaxAmount);
        $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $taxAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTaxAmount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $taxAmount);

        $creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $baseTaxAmount);
        $creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $taxAmount);

        return $this;
    }
}
