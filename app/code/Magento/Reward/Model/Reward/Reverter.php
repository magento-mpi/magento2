<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Reward;

class Reverter
{
    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     */
    public function __construct(
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_rewardFactory = $rewardFactory;
    }

    /**
     * Revert authorized reward points amount for order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function revertRewardPointsForOrder(\Magento\Sales\Model\Order $order)
    {
        if (!$order->getCustomerId()) {
            return $this;
        }
        $this->_rewardFactory->create()->setCustomerId(
            $order->getCustomerId()
        )->setWebsiteId(
            $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
        )->setPointsDelta(
            $order->getRewardPointsBalance()
        )->setAction(
            \Magento\Reward\Model\Reward::REWARD_ACTION_REVERT
        )->setActionEntity(
            $order
        )->updateRewardPoints();

        return $this;
    }
}
