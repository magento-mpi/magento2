<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Model_Total_Creditmemo_Giftcardaccount extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect gift card account totals for credit memo
     *
     * @param Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return Magento_GiftCardAccount_Model_Total_Creditmemo_Giftcardaccount
     */
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getBaseGiftCardsAmount() && $order->getBaseGiftCardsInvoiced() != 0) {
            $gcaLeft = $order->getBaseGiftCardsInvoiced() - $order->getBaseGiftCardsRefunded();

            $used = 0;
            $baseUsed = 0;

            if ($gcaLeft >= $creditmemo->getBaseGrandTotal()) {
                $baseUsed = $creditmemo->getBaseGrandTotal();
                $used = $creditmemo->getGrandTotal();

                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setGrandTotal(0);

                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $baseUsed = $order->getBaseGiftCardsInvoiced() - $order->getBaseGiftCardsRefunded();
                $used = $order->getGiftCardsInvoiced() - $order->getGiftCardsRefunded();

                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$baseUsed);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$used);
            }

            $creditmemo->setBaseGiftCardsAmount($baseUsed);
            $creditmemo->setGiftCardsAmount($used);
        }

        $creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseGiftCardsAmount());

        $creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $creditmemo->getGiftCardsAmount());
        $creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $creditmemo->getGrandTotal());

        return $this;
    }
}
