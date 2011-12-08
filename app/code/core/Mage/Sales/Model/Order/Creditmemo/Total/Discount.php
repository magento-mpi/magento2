<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sales_Model_Order_Creditmemo_Total_Discount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setDiscountAmount(0);
        $creditmemo->setBaseDiscountAmount(0);

        $order = $creditmemo->getOrder();

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;

        /**
         * Calculate how much shipping discount should be applied
         * basing on how much shipping should be refunded.
         */
        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseShippingDiscount = $baseShippingAmount * $order->getBaseShippingDiscountAmount() / $order->getBaseShippingAmount();
            $shippingDiscount = $order->getShippingAmount() * $baseShippingDiscount / $order->getBaseShippingAmount();
            $totalDiscountAmount = $totalDiscountAmount + $shippingDiscount;
            $baseTotalDiscountAmount = $baseTotalDiscountAmount + $baseShippingDiscount;
        }

        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }
            $orderItemDiscount      = (float) $item->getOrderItem()->getDiscountAmount();
            $baseOrderItemDiscount  = (float) $item->getOrderItem()->getBaseDiscountAmount();
            $orderItemQty       = $item->getOrderItem()->getQtyOrdered();

            if ($orderItemDiscount && $orderItemQty) {
                $discount = $orderItemDiscount*$item->getQty()/$orderItemQty;
                $baseDiscount = $baseOrderItemDiscount*$item->getQty()/$orderItemQty;

                $discount = $creditmemo->getStore()->roundPrice($discount);
                $baseDiscount = $creditmemo->getStore()->roundPrice($baseDiscount);

                $item->setDiscountAmount($discount);
                $item->setBaseDiscountAmount($baseDiscount);

                $totalDiscountAmount += $discount;
                $baseTotalDiscountAmount+= $baseDiscount;
            }
        }

        $creditmemo->setDiscountAmount($totalDiscountAmount);
        $creditmemo->setBaseDiscountAmount($baseTotalDiscountAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);
        return $this;
    }
}
