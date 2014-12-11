<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class RevertRewardPointsForAllOrders
{
    /**
     * @var \Magento\Reward\Model\Reward\Reverter
     */
    protected $rewardReverter;

    /**
     * @param \Magento\Reward\Model\Reward\Reverter $reverter
     */
    public function __construct(\Magento\Reward\Model\Reward\Reverter $reverter)
    {
        $this->rewardReverter = $reverter;
    }

    /**
     * Revert authorized reward points amounts for all orders
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orders = $observer->getEvent()->getOrders();

        foreach ($orders as $order) {
            $this->rewardReverter->revertRewardPointsForOrder($order);
        }

        return $this;
    }
}
