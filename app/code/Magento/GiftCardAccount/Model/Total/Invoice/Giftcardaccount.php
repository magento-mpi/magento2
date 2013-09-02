<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Model_Total_Invoice_Giftcardaccount extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect gift card account totals for invoice
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Magento_GiftCardAccount_Model_Total_Invoice_Giftcardaccount
     */
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getBaseGiftCardsAmount() && $order->getBaseGiftCardsInvoiced() != $order->getBaseGiftCardsAmount()) {
            $gcaLeft = $order->getBaseGiftCardsAmount() - $order->getBaseGiftCardsInvoiced();
            $used = 0;
            $baseUsed = 0;
            if ($gcaLeft >= $invoice->getBaseGrandTotal()) {
                $baseUsed = $invoice->getBaseGrandTotal();
                $used = $invoice->getGrandTotal();

                $invoice->setBaseGrandTotal(0);
                $invoice->setGrandTotal(0);
            } else {
                $baseUsed = $order->getBaseGiftCardsAmount() - $order->getBaseGiftCardsInvoiced();
                $used = $order->getGiftCardsAmount() - $order->getGiftCardsInvoiced();

                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$baseUsed);
                $invoice->setGrandTotal($invoice->getGrandTotal()-$used);
            }

            $invoice->setBaseGiftCardsAmount($baseUsed);
            $invoice->setGiftCardsAmount($used);
        }
        return $this;
    }
}
