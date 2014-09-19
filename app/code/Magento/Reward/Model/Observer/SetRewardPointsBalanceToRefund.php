<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class SetRewardPointsBalanceToRefund
{
    /**
     * Set reward points balance to refund before creditmemo register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $input = $observer->getEvent()->getInput();
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (isset($input['refund_reward_points'], $input['refund_reward_points_enable'])
            && $input['refund_reward_points_enable']
        ) {
            $balance = (int)$input['refund_reward_points'];
            $balance = min($creditmemo->getRewardPointsBalance(), $balance);
            if ($balance) {
                $creditmemo->setRewardPointsBalanceRefund($balance);
            }
        }
        return $this;
    }
}
