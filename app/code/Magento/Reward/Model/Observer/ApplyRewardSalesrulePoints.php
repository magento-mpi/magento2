<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class ApplyRewardSalesrulePoints
{
    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\RewardFactory $rewardFactory
    ) {
        $this->_rewardData = $rewardData;
        $this->_rewardFactory = $rewardFactory;
    }

    /**
     * Update customer reward points balance by points from applied sales rules
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        /* @var $order \Magento\Sales\Model\Order */
        $order = $invoice->getOrder();
        //when invoice is updated(commented) we don't apply reward points
        if ($invoice->hasDataChanges()
            || !$this->_rewardData->isEnabledOnFront($order->getStore()->getWebsiteId())
        ) {
            return $this;
        }
        if ($order->getCustomerId() && !$order->canInvoice() && $order->getRewardSalesrulePoints()) {
            $reward = $this->_rewardFactory->create()->setCustomerId(
                $order->getCustomerId()
            )->setWebsiteId(
                $order->getStore()->getWebsiteId()
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_SALESRULE
            )->setActionEntity(
                $order
            )->setPointsDelta(
                $order->getRewardSalesrulePoints()
            )->updateRewardPoints();
            if ($reward->getPointsDelta()) {
                $order->addStatusHistoryComment(
                    __(
                        'Customer earned promotion extra %1.',
                        $this->_rewardData->formatReward($reward->getPointsDelta())
                    )
                )->save();
            }
        }
        return $this;
    }
}
