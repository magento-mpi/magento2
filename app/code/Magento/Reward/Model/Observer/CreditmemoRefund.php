<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class CreditmemoRefund
{
    /**
     * Clear forced can creditmemo if whole reward amount was refunded
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getCreditmemo()->getOrder();
        $refundedAmount = (double)($order->getBaseRwrdCrrncyAmntRefnded() +
            $creditmemo->getBaseRewardCurrencyAmount());
        $rewardAmount = (double)$order->getBaseRwrdCrrncyAmtInvoiced();
        if ($rewardAmount > 0 && $rewardAmount == $refundedAmount) {
            $order->setForcedCanCreditmemo(false);
        }
        return $this;
    }
}
