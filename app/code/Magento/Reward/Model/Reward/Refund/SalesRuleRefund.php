<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Model\Reward\Refund;

class SalesRuleRefund
{
    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $rewardFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Reward Helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $rewardHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Reward\Helper\Data $rewardHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Reward\Helper\Data $rewardHelper
    ) {
        $this->rewardFactory = $rewardFactory;
        $this->storeManager = $storeManager;
        $this->rewardHelper = $rewardHelper;
    }

    /**
     * Refund reward points earned by salesRule
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    public function refund(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $creditmemo->getOrder();
        $totalItemsRefund = $creditmemo->getTotalQty();

        foreach ($order->getCreditmemosCollection() as $creditMemo) {
            foreach ($creditMemo->getAllItems() as $item) {
                $totalItemsRefund += $item->getQty();
            }
        }

        $isRefundAllowed = false;
        if ($creditmemo->getAutomaticallyCreated()) {
            if ($this->rewardHelper->isAutoRefundEnabled()) {
                $isRefundAllowed = true;
            }
            $creditmemo->setRewardPointsBalanceRefund($creditmemo->getRewardPointsBalance());
        } else {
            $isRefundAllowed = true;
        }

        if ($isRefundAllowed
            && $order->getRewardSalesrulePoints() > 0
            && $order->getTotalQtyOrdered() - $totalItemsRefund == 0
        ) {
            $rewardModel = $this->rewardFactory->create();
            $rewardModel->setWebsiteId(
                $this->storeManager->getStore($order->getStoreId())->getWebsiteId()
            )->setCustomerId(
                $order->getCustomerId()
            )->loadByCustomer();

            if ($rewardModel->getPointsBalance() >= $order->getRewardSalesrulePoints()) {
                $rewardPointsToVoid = (int)$order->getRewardSalesrulePoints();
            } else {
                $rewardPointsToVoid = (int)$rewardModel->getPointsBalance();
            }

            $this->rewardFactory->create()->setCustomerId(
                $order->getCustomerId()
            )->setWebsiteId(
                $this->storeManager->getStore($order->getStoreId())->getWebsiteId()
            )->setPointsDelta(
                (-$rewardPointsToVoid)
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO_VOID
            )->setActionEntity(
                $order
            )->save();
        }
    }
}
