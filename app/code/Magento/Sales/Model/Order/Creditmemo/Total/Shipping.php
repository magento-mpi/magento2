<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order creditmemo shipping total calculation model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Creditmemo_Total_Shipping extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $allowedAmount          = $order->getShippingAmount()-$order->getShippingRefunded();
        $baseAllowedAmount      = $order->getBaseShippingAmount()-$order->getBaseShippingRefunded();

        $shipping               = $order->getShippingAmount();
        $baseShipping           = $order->getBaseShippingAmount();
        $shippingInclTax        = $order->getShippingInclTax();
        $baseShippingInclTax    = $order->getBaseShippingInclTax();

        $isShippingInclTax = Mage::getSingleton('Magento_Tax_Model_Config')->displaySalesShippingInclTax($order->getStoreId());

        /**
         * Check if shipping amount was specified (from invoice or another source).
         * Using has magic method to allow setting 0 as shipping amount.
         */
        if ($creditmemo->hasBaseShippingAmount()) {
            $baseShippingAmount = Mage::app()->getStore()->roundPrice($creditmemo->getBaseShippingAmount());
            if ($isShippingInclTax && $baseShippingInclTax != 0) {
                $part = $baseShippingAmount/$baseShippingInclTax;
                $shippingInclTax    = Mage::app()->getStore()->roundPrice($shippingInclTax*$part);
                $baseShippingInclTax= $baseShippingAmount;
                $baseShippingAmount = Mage::app()->getStore()->roundPrice($baseShipping*$part);
            }
            /*
             * Rounded allowed shipping refund amount is the highest acceptable shipping refund amount.
             * Shipping refund amount shouldn't cause errors, if it doesn't exceed that limit.
             * Note: ($x < $y + 0.0001) means ($x <= $y) for floats
             */
            if ($baseShippingAmount < Mage::app()->getStore()->roundPrice($baseAllowedAmount) + 0.0001) {
                /*
                 * Shipping refund amount should be equated to allowed refund amount,
                 * if it exceeds that limit.
                 * Note: ($x > $y - 0.0001) means ($x >= $y) for floats
                 */
                if ($baseShippingAmount > $baseAllowedAmount - 0.0001) {
                    $shipping     = $allowedAmount;
                    $baseShipping = $baseAllowedAmount;
                } else {
                    if ($baseShipping != 0) {
                        $shipping = $shipping * $baseShippingAmount / $baseShipping;
                    }
                    $shipping     = Mage::app()->getStore()->roundPrice($shipping);
                    $baseShipping = $baseShippingAmount;
                }
            } else {
                $baseAllowedAmount = $order->getBaseCurrency()->format($baseAllowedAmount,null,false);
                Mage::throwException(
                    __('Maximum shipping amount allowed to refund is: %1', $baseAllowedAmount)
                );
            }
        } else {
            if ($baseShipping != 0) {
                $allowedTaxAmount = $order->getShippingTaxAmount() - $order->getShippingTaxRefunded();
                $baseAllowedTaxAmount = $order->getBaseShippingTaxAmount() - $order->getBaseShippingTaxRefunded();

                $shippingInclTax = Mage::app()->getStore()->roundPrice($allowedAmount + $allowedTaxAmount);
                $baseShippingInclTax = Mage::app()->getStore()->roundPrice($baseAllowedAmount + $baseAllowedTaxAmount);
            }
            $shipping           = $allowedAmount;
            $baseShipping       = $baseAllowedAmount;
        }

        $creditmemo->setShippingAmount($shipping);
        $creditmemo->setBaseShippingAmount($baseShipping);
        $creditmemo->setShippingInclTax($shippingInclTax);
        $creditmemo->setBaseShippingInclTax($baseShippingInclTax);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$shipping);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$baseShipping);
        return $this;
    }
}
