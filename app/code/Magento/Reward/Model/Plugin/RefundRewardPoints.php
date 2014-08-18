<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Model\Plugin;

class RefundRewardPoints
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Reward history collection
     *
     * @var \Magento\Reward\Model\Resource\Reward\History\CollectionFactory
     */
    protected $_historyCollectionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    protected $_creditmemo;

    /**
     * @param \Magento\Reward\Model\Resource\Reward\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Event\ManagerInterface
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Reward\Helper\Data $rewardData
     */
    public function __construct(
        \Magento\Reward\Model\Resource\Reward\History\CollectionFactory $historyCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Reward\Helper\Data $rewardData
    ) {
        $this->_historyCollectionFactory = $historyCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_rewardFactory = $rewardFactory;
        $this->_rewardData = $rewardData;
    }

    public function aroundSave(
        \Magento\Sales\Model\Resource\Order\Creditmemo $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        $result = $proceed($object);

        $this->_creditmemo = $object;
        $this->_order = $this->_creditmemo->getOrder();

        if ($this->_creditmemo->getBaseRewardCurrencyAmount() && $this->_isAllowRewardPointsRefund()) {
            $this->_order->setRewardPointsBalanceRefunded(
                $this->_order->getRewardPointsBalanceRefunded() + $this->_creditmemo->getRewardPointsBalance()
            );
            $this->_order->setRwrdCrrncyAmntRefunded(
                $this->_order->getRwrdCrrncyAmntRefunded() + $this->_creditmemo->getRewardCurrencyAmount()
            );
            $this->_order->setBaseRwrdCrrncyAmntRefnded(
                $this->_order->getBaseRwrdCrrncyAmntRefnded() + $this->_creditmemo->getBaseRewardCurrencyAmount()
            );
            $this->_order->setRewardPointsBalanceRefund(
                $this->_order->getRewardPointsBalanceRefund() + $this->_creditmemo->getRewardPointsBalanceRefund()
            );

            if ((int)$this->_creditmemo->getRewardPointsBalanceRefund() > 0) {
                $this->_getRewardModel()->setCustomerId(
                    $this->_order->getCustomerId()
                )->setStore(
                    $this->_order->getStoreId()
                )->setPointsDelta(
                    (int)$this->_creditmemo->getRewardPointsBalanceRefund()
                )->setAction(
                    \Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO
                )->setActionEntity(
                    $this->_order
                )->save();
            }
        }

        $this->_updateHistoryRow();
        $this->_refundRewardPointsEarnedBySalesRule();

        return $result;
    }

    /**
     * Refund reward points earned by salesRule
     *
     */
    protected function _refundRewardPointsEarnedBySalesRule()
    {
        $totalItemsRefund = $this->_creditmemo->getTotalQty();
        foreach ($this->_order->getCreditmemosCollection() as $creditMemo) {
            foreach ($creditMemo->getAllItems() as $item) {
                $totalItemsRefund += $item->getQty();
            }
        }

        if ($this->_isAllowRewardPointsRefund()
            && $this->_order->getRewardSalesrulePoints() > 0
            && $this->_order->getTotalQtyOrdered() - $totalItemsRefund == 0
        ) {
            $rewardModel = $this->_getRewardModel();
            $rewardModel->setWebsiteId(
                $this->_storeManager->getStore($this->_order->getStoreId())->getWebsiteId()
            )->setCustomerId(
                $this->_order->getCustomerId()
            )->loadByCustomer();

            if ($rewardModel->getPointsBalance() >= $this->_order->getRewardSalesrulePoints()) {
                $rewardPointsToVoid = (int)$this->_order->getRewardSalesrulePoints();
            } else {
                $rewardPointsToVoid = (int)$rewardModel->getPointsBalance();
            }

            $this->_getRewardModel()->setCustomerId(
                $this->_order->getCustomerId()
            )->setWebsiteId(
                $this->_storeManager->getStore($this->_order->getStoreId())->getWebsiteId()
            )->setPointsDelta(
                (-$rewardPointsToVoid)
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO_VOID
            )->setActionEntity(
                $this->_order
            )->save();
        }
    }

    /**
     * Update reward history row
     *
     * @return bool
     */
    protected function _updateHistoryRow()
    {
        // Void reward points granted for refunded amount if there was any
        $rewardHistoryRecord = $this->_getRewardHistoryRecordForOrder();

        if (!$rewardHistoryRecord) {
            return false;
        }

        /* Calculating amount of funds from total refunded amount for which reward points were acquired */
        $rewardedAmountForWholeOrder = $this->_order->getBaseGrandTotal() - $this->_order->getBaseTaxAmount()
            - $this->_order->getBaseShippingAmount();
        $rewardedAmountRefunded = $this->_order->getBaseTotalRefunded() - $this->_order->getBaseTaxRefunded()
            - $this->_order->getBaseShippingRefunded();
        $rewardedAmountAfterRefund = $rewardedAmountForWholeOrder - $rewardedAmountRefunded;

        /* Modify amount for which reward points should not be voided at refund */
        $this->_creditmemo->setRewardedAmountAfterRefund($rewardedAmountAfterRefund);
        $this->_eventManager->dispatch(
            'rewarded_amount_after_refund_calculation',
            array('creditmemo' => $this->_creditmemo)
        );
        $rewardedAmountAfterRefund = $this->_creditmemo->getRewardedAmountAfterRefund();

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
            return false;
        }

        $reward = $this->_getRewardModel()
            ->setWebsiteId($this->_storeManager->getStore($this->_order->getStoreId())->getWebsiteId())
            ->setCustomerId($this->_order->getCustomerId())
            ->loadByCustomer();

        $rewardPointsBalance = $reward->getPointsBalance();

        if ($rewardPointsBalance <= 0) {
            return false;
        }

        // It's not allowed to void more points then is available for current customer
        if ($rewardPointsAmountToVoid > $rewardPointsBalance) {
            $rewardPointsAmountToVoid = $rewardPointsBalance;
        }

        if ($this->_rewardData->getGeneralConfig('deduct_automatically')) {
            $reward->setPointsDelta(-$rewardPointsAmountToVoid)
                ->setAction(\Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO_VOID)
                ->setActionEntity($this->_order)
                ->updateRewardPoints();

            if ($reward->getRewardPointsUpdated()) {
                $this->_order->addStatusHistoryComment(__(
                    '%1 was deducted because of refund.',
                    $this->_rewardData->formatReward($rewardPointsAmountToVoid)
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
        return true;
    }

    /**
     * Get is allow reward points refund for this order
     *
     * @return bool
     */
    protected function _isAllowRewardPointsRefund()
    {
        if ($this->_creditmemo->getAutomaticallyCreated()) {
            if (!$this->_rewardData->isAutoRefundEnabled()) {
                return false;
            }
            $this->_creditmemo->setRewardPointsBalanceRefund($this->_creditmemo->getRewardPointsBalance());
        }
        return true;
    }

    /**
     * Get reward history model for current order
     *
     * @return \Magento\Reward\Model\Reward\History|null
     */
    protected function _getRewardHistoryRecordForOrder()
    {
        $rewardHistoryCollection = $this->_historyCollectionFactory->create()
            ->addCustomerFilter($this->_order->getCustomerId())
            ->addWebsiteFilter($this->_order->getStore()->getWebsiteId())
            // nothing to void if reward points are expired already
            ->addFilter('main_table.is_expired', 0)
            // void points acquired for purchase only
            ->addFilter('main_table.action', \Magento\Reward\Model\Reward::REWARD_ACTION_ORDER_EXTRA);

        foreach ($rewardHistoryCollection as $rewardHistoryRecord) {
            $additionalData = $rewardHistoryRecord->getAdditionalData();
            if (isset($additionalData['increment_id'])
                && $additionalData['increment_id'] == $this->_order->getIncrementId()
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
    protected function _getRewardModel()
    {
        return $this->_rewardFactory->create();
    }
} 