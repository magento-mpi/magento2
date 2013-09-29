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
 * Reward sales order creditmemo total model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Total\Creditmemo;

class Reward extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Collect reward totals for credit memo
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return \Magento\Reward\Model\Total\Creditmemo\Reward
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $rewardCurrecnyAmountLeft = $order->getRwrdCurrencyAmountInvoiced() - $order->getRwrdCrrncyAmntRefunded();
        $baseRewardCurrecnyAmountLeft = $order->getBaseRwrdCrrncyAmtInvoiced() - $order->getBaseRwrdCrrncyAmntRefnded();
        if ($order->getBaseRewardCurrencyAmount() && $baseRewardCurrecnyAmountLeft > 0) {
            if ($baseRewardCurrecnyAmountLeft >= $creditmemo->getBaseGrandTotal()) {
                $rewardCurrecnyAmountLeft = $creditmemo->getGrandTotal();
                $baseRewardCurrecnyAmountLeft = $creditmemo->getBaseGrandTotal();
                $creditmemo->setGrandTotal(0);
                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $rewardCurrecnyAmountLeft);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseRewardCurrecnyAmountLeft);
            }
            $pointValue = $order->getRewardPointsBalance() / $order->getBaseRewardCurrencyAmount();
            $rewardPointsBalance = $baseRewardCurrecnyAmountLeft*ceil($pointValue);
            $rewardPointsBalanceLeft = $order->getRewardPointsBalance() - $order->getRewardPointsBalanceRefunded();
            if ($rewardPointsBalance > $rewardPointsBalanceLeft) {
                $rewardPointsBalance = $rewardPointsBalanceLeft;
            }
            $creditmemo->setRewardPointsBalance($rewardPointsBalance);
            $creditmemo->setRewardCurrencyAmount($rewardCurrecnyAmountLeft);
            $creditmemo->setBaseRewardCurrencyAmount($baseRewardCurrecnyAmountLeft);
        }
        return $this;
    }
}
