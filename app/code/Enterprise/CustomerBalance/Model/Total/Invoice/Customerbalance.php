<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerBalance_Model_Total_Invoice_Customerbalance extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect customer balance totals for invoice
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Enterprise_CustomerBalance_Model_Total_Invoice_Customerbalance
     */
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
    {
        if (!Mage::helper('Enterprise_CustomerBalance_Helper_Data')->isEnabled()) {
            return $this;
        }
        $order = $invoice->getOrder();
        if ($order->getBaseCustomerBalanceAmount() && $order->getBaseCustomerBalanceInvoiced() != $order->getBaseCustomerBalanceAmount()) {
            $gcaLeft = $order->getBaseCustomerBalanceAmount() - $order->getBaseCustomerBalanceInvoiced();
            $used = 0;
            $baseUsed = 0;
            if ($gcaLeft >= $invoice->getBaseGrandTotal()) {
                $baseUsed = $invoice->getBaseGrandTotal();
                $used = $invoice->getGrandTotal();

                $invoice->setBaseGrandTotal(0);
                $invoice->setGrandTotal(0);
            } else {
                $baseUsed = $order->getBaseCustomerBalanceAmount() - $order->getBaseCustomerBalanceInvoiced();
                $used = $order->getCustomerBalanceAmount() - $order->getCustomerBalanceInvoiced();

                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$baseUsed);
                $invoice->setGrandTotal($invoice->getGrandTotal()-$used);
            }

            $invoice->setBaseCustomerBalanceAmount($baseUsed);
            $invoice->setCustomerBalanceAmount($used);
        }
        return $this;
    }
}
