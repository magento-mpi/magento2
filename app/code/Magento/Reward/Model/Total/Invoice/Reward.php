<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward sales order invoice total model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Total\Invoice;

class Reward extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * Collect reward total for invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Magento\Reward\Model\Total\Invoice\Reward
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
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
