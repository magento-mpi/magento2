<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward sales order invoice total model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Total_Invoice_Reward extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect reward total for invoice
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Enterprise_Reward_Model_Total_Invoice_Reward
     */
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $rewardCurrecnyAmountLeft = $order->getRewardCurrencyAmount() - $order->getRwrdCurrencyAmountInvoiced();
        $baseRewardCurrecnyAmountLeft = $order->getBaseRewardCurrencyAmount() - $order->getBaseRwrdCrrncyAmtInvoiced();
        if ($order->getBaseRewardCurrencyAmount() && $baseRewardCurrecnyAmountLeft > 0) {
            if ($baseRewardCurrecnyAmountLeft < $invoice->getBaseGrandTotal()) {
                $invoice->setGrandTotal($invoice->getGrandTotal() - $rewardCurrecnyAmountLeft);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseRewardCurrecnyAmountLeft);
            } else {
                $rewardCurrecnyAmountLeft = $invoice->getGrandTotal();
                $baseRewardCurrecnyAmountLeft = $invoice->getBaseGrandTotal();

                $invoice->setGrandTotal(0);
                $invoice->setBaseGrandTotal(0);
            }
            $pointValue = $order->getRewardPointsBalance() / $order->getBaseRewardCurrencyAmount();
            $rewardPointsBalance = $baseRewardCurrecnyAmountLeft*ceil($pointValue);
            $rewardPointsBalanceLeft = $order->getRewardPointsBalance() - $order->getRewardPointsBalanceInvoiced();
            if ($rewardPointsBalance > $rewardPointsBalanceLeft) {
                $rewardPointsBalance = $rewardPointsBalanceLeft;
            }
            $invoice->setRewardPointsBalance($rewardPointsBalance);
            $invoice->setRewardCurrencyAmount($rewardCurrecnyAmountLeft);
            $invoice->setBaseRewardCurrencyAmount($baseRewardCurrecnyAmountLeft);
        }
        return $this;
    }
}
