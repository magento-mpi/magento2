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
 * GiftWrapping total calculator for creditmemo
 *
 */
class Enterprise_GiftWrapping_Model_Total_Creditmemo_Giftwrapping extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect gift wrapping totals
     *
     * @param   Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return  Enterprise_GiftWrapping_Model_Total_Creditmemo_Giftwrapping
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
            if ($orderItem->getGwId() && $orderItem->getGwBasePriceInvoiced()
                && $orderItem->getGwBasePriceInvoiced() != $orderItem->getGwBasePriceRefunded()) {
                $orderItem->setGwBasePriceRefunded($orderItem->getGwBasePriceInvoiced());
                $orderItem->setGwPriceRefunded($orderItem->getGwPriceInvoiced());
                $baseRefunded += $orderItem->getGwBasePriceInvoiced();
                $refunded += $orderItem->getGwPriceInvoiced();
            }
        }
        if ($refunded > 0 || $baseRefunded > 0) {
            $order->setGwItemsBasePriceRefunded($order->getGwItemsBasePriceRefunded() + $baseRefunded);
            $order->setGwItemsPriceRefunded($order->getGwItemsPriceRefunded() + $refunded);
            $creditmemo->setGwItemsBasePrice($baseRefunded);
            $creditmemo->setGwItemsPrice($refunded);
        }

        /**
         * Wrapping for order
         */
        if ($order->getGwId() && $order->getGwBasePriceInvoiced()
            && $order->getGwBasePriceInvoiced() != $order->getGwBasePriceRefunded()) {
            $order->setGwBasePriceRefunded($order->getGwBasePriceInvoiced());
            $order->setGwPriceRefunded($order->getGwPriceInvoiced());
            $creditmemo->setGwBasePrice($order->getGwBasePriceInvoiced());
            $creditmemo->setGwPrice($order->getGwPriceInvoiced());
        }

        /**
         * Printed card
         */
        if ($order->getGwAddCard() && $order->getGwCardBasePriceInvoiced()
            && $order->getGwCardBasePriceInvoiced() != $order->getGwCardBasePriceRefunded()) {
            $order->setGwCardBasePriceRefunded($order->getGwCardBasePriceInvoiced());
            $order->setGwCardPriceRefunded($order->getGwCardPriceInvoiced());
            $creditmemo->setGwCardBasePrice($order->getGwCardBasePriceInvoiced());
            $creditmemo->setGwCardPrice($order->getGwCardPriceInvoiced());
        }

        $creditmemo->setBaseGrandTotal(
            $creditmemo->getBaseGrandTotal()
            + $creditmemo->getGwItemsBasePrice()
            + $creditmemo->getGwBasePrice()
            + $creditmemo->getGwCardBasePrice()
        );
        $creditmemo->setGrandTotal(
            $creditmemo->getGrandTotal()
            + $creditmemo->getGwItemsPrice()
            + $creditmemo->getGwPrice()
            + $creditmemo->getGwCardPrice()
        );

        $creditmemo->setBaseCustomerBalanceReturnMax(
            $creditmemo->getBaseCustomerBalanceReturnMax()
            + $creditmemo->getGwCardBasePrice()
            + $creditmemo->getGwBasePrice()
            + $creditmemo->getGwItemsBasePrice()
        );
        $creditmemo->setCustomerBalanceReturnMax(
            $creditmemo->getCustomerBalanceReturnMax()
            + $creditmemo->getGwCardPrice()
            + $creditmemo->getGwPrice()
            + $creditmemo->getGwItemsPrice()
        );

        return $this;
    }
}
