<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Model\Plugin;

class RewardPointsRefund
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $rewardData;

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
     * Reward history collection
     *
     * @var \Magento\Reward\Model\Resource\Reward\History\CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Event manager
     *
     * @var \Magento\Reward\Model\Reward\RewardRefund
     */
    protected $rewardRefund;

    /**
     * @param \Magento\Reward\Model\Resource\Reward\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Event\ManagerInterface
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\Reward\RewardRefund $rewardRefund
     */
    public function __construct(
        \Magento\Reward\Model\Resource\Reward\History\CollectionFactory $historyCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\Reward\RewardRefund $rewardRefund
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->storeManager = $storeManager;
        $this->eventManager = $eventManager;
        $this->rewardFactory = $rewardFactory;
        $this->rewardData = $rewardData;
        $this->rewardRefund = $rewardRefund;
    }

    /**
     * Reward points refund after creditmemo creation
     *
     * @param \Magento\Sales\Model\Resource\Order\Creditmemo $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function aroundSave(
        \Magento\Sales\Model\Resource\Order\Creditmemo $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        $result = $proceed($object);

        /* @var $creditmemo \Magento\Sales\Model\Order\Creditmemo */
        $creditmemo = $object;

        /* @var $order \Magento\Sales\Model\Order */
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseRewardCurrencyAmount() && $this->isRewardPointsRefundAllowed($creditmemo)) {
            $order->setRewardPointsBalanceRefunded(
                $order->getRewardPointsBalanceRefunded() + $creditmemo->getRewardPointsBalance()
            );
            $order->setRwrdCrrncyAmntRefunded(
                $order->getRwrdCrrncyAmntRefunded() + $creditmemo->getRewardCurrencyAmount()
            );
            $order->setBaseRwrdCrrncyAmntRefnded(
                $order->getBaseRwrdCrrncyAmntRefnded() + $creditmemo->getBaseRewardCurrencyAmount()
            );
            $order->setRewardPointsBalanceRefund(
                $order->getRewardPointsBalanceRefund() + $creditmemo->getRewardPointsBalanceRefund()
            );

            if ((int)$creditmemo->getRewardPointsBalanceRefund() > 0) {
                $this->getRewardModel()->setCustomerId(
                    $order->getCustomerId()
                )->setStore(
                    $order->getStoreId()
                )->setPointsDelta(
                    (int)$order->getRewardPointsBalanceRefund()
                )->setAction(
                    \Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO
                )->setActionEntity(
                    $order
                )->save();
            }
        }

        $this->updateHistoryRow($creditmemo);
        $this->rewardRefund->refundRewardPointsEarnedBySalesRule($creditmemo);

        return $result;
    }

    /**
     * Update reward history row
     *
     */
    protected function updateHistoryRow(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $creditmemo->getOrder();

        // Void reward points granted for refunded amount if there was any
        $rewardHistoryRecord = $this->getRewardHistoryRecordForOrder($order);

        if (!$rewardHistoryRecord) {
            return;
        }

        /* Calculating amount of funds from total refunded amount for which reward points were acquired */
        $rewardedAmountForWholeOrder = $order->getBaseGrandTotal() - $order->getBaseTaxAmount()
            - $order->getBaseShippingAmount();
        $rewardedAmountRefunded = $order->getBaseTotalRefunded() - $order->getBaseTaxRefunded()
            - $order->getBaseShippingRefunded();
        $rewardedAmountAfterRefund = $rewardedAmountForWholeOrder - $rewardedAmountRefunded;

        /* Modify amount for which reward points should not be voided at refund */
        $creditmemo->setRewardedAmountAfterRefund($rewardedAmountAfterRefund);
        $this->eventManager->dispatch(
            'rewarded_amount_after_refund_calculation',
            array('creditmemo' => $creditmemo)
        );
        $rewardedAmountAfterRefund = $creditmemo->getRewardedAmountAfterRefund();

        /* Calculating amount of points to void considering reward points exchange rate when they were granted */
        $additionalData = $rewardHistoryRecord->getAdditionalData();
        $estimatedRewardPointsAfterRefund = (int)((string)$rewardedAmountAfterRefund /
                (string)$additionalData['rate']['currency_amount']) * $additionalData['rate']['points'];
        $rewardPointsVoided = $rewardHistoryRecord->getPointsVoided();
        $acquiredRewardPointsAvailableForVoid = $rewardHistoryRecord->getPointsDelta() - $rewardPointsVoided;

        /*
         * It's not allowed to void more points then were granted per this order.
         * Used points at current history record are not taken into consideration -
         * allowed to void from total amount if it's needed to void more then left at selected history record.
         */
        $rewardPointsAmountToVoid = 0;
        if ($acquiredRewardPointsAvailableForVoid > $estimatedRewardPointsAfterRefund) {
            $rewardPointsAmountToVoid = $acquiredRewardPointsAvailableForVoid - $estimatedRewardPointsAfterRefund;
        }

        if ($rewardPointsAmountToVoid <= 0) {
            return;
        }

        $reward = $this->getRewardModel()
            ->setWebsiteId($this->storeManager->getStore($order->getStoreId())->getWebsiteId())
            ->setCustomerId($order->getCustomerId())
            ->loadByCustomer();

        $rewardPointsBalance = $reward->getPointsBalance();

        if ($rewardPointsBalance <= 0) {
            return;
        }

        // It's not allowed to void more points then is available for current customer
        if ($rewardPointsAmountToVoid > $rewardPointsBalance) {
            $rewardPointsAmountToVoid = $rewardPointsBalance;
        }

        if ($this->rewardData->getGeneralConfig('deduct_automatically')) {
            $reward->setPointsDelta(-$rewardPointsAmountToVoid)
                ->setAction(\Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO_VOID)
                ->setActionEntity($order)
                ->updateRewardPoints();

            if ($reward->getRewardPointsUpdated()) {
                $order->addStatusHistoryComment(__(
                    '%1 was deducted because of refund.',
                    $this->rewardData->formatReward($rewardPointsAmountToVoid)
                ));
            }
        }

        /*
         * Config option deduct_automatically is not considered here because points for refunded amount that
         * were not been voided automatically need to be counted in possible future automatic deductions.
         */
        $rewardHistoryRecord->getResource()->updateHistoryRow($rewardHistoryRecord, array(
            'points_voided' => $rewardPointsVoided + $rewardPointsAmountToVoid
        ));
    }

    /**
     * Get is allow reward points refund for this order
     *
     * @return bool
     */
    protected function isRewardPointsRefundAllowed(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        if ($creditmemo->getAutomaticallyCreated()) {
            if (!$this->rewardData->isAutoRefundEnabled()) {
                return false;
            }
            $creditmemo->setRewardPointsBalanceRefund($creditmemo->getRewardPointsBalance());
        }
        return true;
    }

    /**
     * Get reward history model for current order
     *
     * @return \Magento\Reward\Model\Reward\History|null
     */
    protected function getRewardHistoryRecordForOrder(\Magento\Sales\Model\Order $order)
    {
        $rewardHistoryCollection = $this->historyCollectionFactory->create()
            ->addCustomerFilter($order->getCustomerId())
            ->addWebsiteFilter($order->getStore()->getWebsiteId())
            // nothing to void if reward points are expired already
            ->addFilter('main_table.is_expired', 0)
            // void points acquired for purchase only
            ->addFilter('main_table.action', \Magento\Reward\Model\Reward::REWARD_ACTION_ORDER_EXTRA);

        foreach ($rewardHistoryCollection as $rewardHistoryRecord) {
            $additionalData = $rewardHistoryRecord->getAdditionalData();
            if (isset($additionalData['increment_id'])
                && $additionalData['increment_id'] == $order->getIncrementId()
                && isset($additionalData['rate']['direction'])
                && $additionalData['rate']['direction'] ==
                \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
                && isset($additionalData['rate']['points'])
                && isset($additionalData['rate']['currency_amount'])
            ) {
                return $rewardHistoryRecord;
            }
        }
        return null;
    }

    /**
     * Get reward model
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function getRewardModel()
    {
        return $this->rewardFactory->create();
    }
} 