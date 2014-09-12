<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model;

use Magento\Customer\Model\Converter;

/**
 * Reward observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Observer
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Core model store configuration
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Reward history collection
     *
     * @var \Magento\Reward\Model\Resource\Reward\History\CollectionFactory
     */
    protected $_historyCollectionFactory;

    /**
     * Reward history factory
     *
     * @var \Magento\Reward\Model\Resource\Reward\HistoryFactory
     */
    protected $_historyItemFactory;

    /**
     * Reward factory
     * @var \Magento\Reward\Model\Resource\RewardFactory
     */
    protected $_rewardResourceFactory;

    /**
     * Reward rate factory
     * @var \Magento\Reward\Model\Reward\RateFactory
     */
    protected $_rateFactory;

    /** @var Converter */
    protected $_customerConverter;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param RewardFactory $rewardFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Resource\Reward\History\CollectionFactory $historyCollectionFactory
     * @param Resource\Reward\HistoryFactory $historyItemFactory
     * @param Resource\RewardFactory $rewardResourceFactory
     * @param Reward\RateFactory $rateFactory
     * @param Converter $customerConverter
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Reward\Model\Resource\Reward\History\CollectionFactory $historyCollectionFactory,
        \Magento\Reward\Model\Resource\Reward\HistoryFactory $historyItemFactory,
        \Magento\Reward\Model\Resource\RewardFactory $rewardResourceFactory,
        \Magento\Reward\Model\Reward\RateFactory $rateFactory,
        Converter $customerConverter
    ) {
        $this->_coreData = $coreData;
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_rewardFactory = $rewardFactory;
        $this->_logger = $logger;
        $this->_invitationFactory = $invitationFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_historyCollectionFactory = $historyCollectionFactory;
        $this->_historyItemFactory = $historyItemFactory;
        $this->_rewardResourceFactory = $rewardResourceFactory;
        $this->_rateFactory = $rateFactory;
        $this->_customerConverter = $customerConverter;
    }

    /**
     * Update reward points for customer, send notification
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function saveRewardPoints($observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return;
        }

        $request = $observer->getEvent()->getRequest();
        $data = $request->getPost('reward');
        if ($data && !empty($data['points_delta'])) {
            /** @var \Magento\Customer\Service\V1\Data\Customer $customer */
            $customer = $observer->getEvent()->getCustomer();

            if (!isset($data['store_id'])) {
                if ($customer->getStoreId() == 0) {
                    $data['store_id'] = $this->_storeManager->getDefaultStoreView()->getStoreId();
                } else {
                    $data['store_id'] = $customer->getStoreId();
                }
            }
            $customerModel = $this->_customerConverter->getCustomerModel($customer->getId());
            /** @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_getRewardModel();
            $reward->setCustomer(
                $customerModel
            )->setWebsiteId(
                $this->_storeManager->getStore($data['store_id'])->getWebsiteId()
            )->loadByCustomer();

            $reward->addData($data);
            $reward->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_ADMIN
            )->setActionEntity(
                $customerModel
            )->updateRewardPoints();
        }
        return $this;
    }

    /**
     * Update reward notifications for customer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function saveRewardNotifications($observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }

        $request = $observer->getEvent()->getRequest();
        /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder $customer */
        $customerBuilder = $observer->getEvent()->getCustomer();

        /*
         * Customer builder was passed to event in order to provide possibility to observer to change
         * the data of the Customer Data Object.
         * Now we're constructing the Customer object from the builder in order to read the data
         * and populate Builder back with it.
         */
        $customer = $customerBuilder->create();
        $customerBuilder->populate($customer);

        $data = $request->getPost('reward');
        // If new customer
        if (!$customer->getId()) {
            $subscribeByDefault = (int)$this->_rewardData->getNotificationConfig(
                'subscribe_by_default',
                (int)$customer->getWebsiteId()
            );
            $data['reward_update_notification'] = $subscribeByDefault;
            $data['reward_warning_notification'] = $subscribeByDefault;
        }

        $customerBuilder->setCustomAttribute(
            'reward_update_notification',
            empty($data['reward_update_notification']) ? 0 : 1
        );
        $customerBuilder->setCustomAttribute(
            'reward_warning_notification',
            empty($data['reward_warning_notification']) ? 0 : 1
        );

        return $this;
    }

    /**
     * Update reward points after customer register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function customerRegister($observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }
        /* @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getEvent()->getCustomer();
        $customerOrigData = $customer->getOrigData();
        if (empty($customerOrigData)) {
            try {
                $subscribeByDefault = $this->_rewardData->getNotificationConfig(
                    'subscribe_by_default',
                    $this->_storeManager->getStore()->getWebsiteId()
                );
                $reward = $this->_getRewardModel()->setCustomer(
                    $customer
                )->setActionEntity(
                    $customer
                )->setStore(
                    $this->_storeManager->getStore()->getId()
                )->setAction(
                    \Magento\Reward\Model\Reward::REWARD_ACTION_REGISTER
                )->updateRewardPoints();

                $customer->setRewardUpdateNotification(
                    (int)$subscribeByDefault
                )->setRewardWarningNotification(
                    (int)$subscribeByDefault
                );
                $customer->getResource()->saveAttribute($customer, 'reward_update_notification');
                $customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
            } catch (\Exception $e) {
                //save exception if something were wrong during saving reward and allow to register customer
                $this->_logger->logException($e);
            }
        }
        return $this;
    }

    /**
     * Update points balance after review submit
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function reviewSubmit($observer)
    {
        /* @var $review \Magento\Review\Model\Review */
        $review = $observer->getEvent()->getObject();
        $websiteId = $this->_storeManager->getStore($review->getStoreId())->getWebsiteId();
        if (!$this->_rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }
        if ($review->isApproved() && $review->getCustomerId()) {
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_getRewardModel()->setCustomerId(
                $review->getCustomerId()
            )->setStore(
                $review->getStoreId()
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_REVIEW
            )->setActionEntity(
                $review
            )->updateRewardPoints();
        }
        return $this;
    }

    /**
     * Update points balance after first successful subscribtion
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function customerSubscribed($observer)
    {
        /* @var $subscriber \Magento\Newsletter\Model\Subscriber */
        $subscriber = $observer->getEvent()->getSubscriber();
        // reward only new subscribtions
        if (!$subscriber->isObjectNew() || !$subscriber->getCustomerId()) {
            return $this;
        }
        $websiteId = $this->_storeManager->getStore($subscriber->getStoreId())->getWebsiteId();
        if (!$this->_rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }

        $reward = $this->_getRewardModel()->setCustomerId(
            $subscriber->getCustomerId()
        )->setStore(
            $subscriber->getStoreId()
        )->setAction(
            \Magento\Reward\Model\Reward::REWARD_ACTION_NEWSLETTER
        )->setActionEntity(
            $subscriber
        )->updateRewardPoints();

        return $this;
    }

    /**
     * Update points balance after customer registered by invitation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function invitationToCustomer($observer)
    {
        /* @var $invitation \Magento\Invitation\Model\Invitation */
        $invitation = $observer->getEvent()->getInvitation();
        $websiteId = $this->_storeManager->getStore($invitation->getStoreId())->getWebsiteId();
        if (!$this->_rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }

        if ($invitation->getCustomerId() && $invitation->getReferralId()) {
            $this->_getRewardModel()->setCustomerId(
                $invitation->getCustomerId()
            )->setWebsiteId(
                $websiteId
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_INVITATION_CUSTOMER
            )->setActionEntity(
                $invitation
            )->updateRewardPoints();
        }

        return $this;
    }

    /**
     * Update points balance after order becomes completed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function orderCompleted($observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->getCustomerIsGuest() || !$this->_rewardData->isEnabledOnFront($order->getStore()->getWebsiteId())
        ) {
            return $this;
        }

        if ($order->getCustomerId() && $this->_isOrderPaidNow($order)) {
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_getRewardModel()->setActionEntity(
                $order
            )->setCustomerId(
                $order->getCustomerId()
            )->setWebsiteId(
                $order->getStore()->getWebsiteId()
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_ORDER_EXTRA
            )->updateRewardPoints();
            if ($reward->getRewardPointsUpdated() && $reward->getPointsDelta()) {
                $order->addStatusHistoryComment(
                    __(
                        'The customer earned %1 for this order.',
                        $this->_rewardData->formatReward($reward->getPointsDelta())
                    )
                )->save();
            }
        }

        return $this;
    }

    /**
     * Check if order is paid exactly now
     * If order was paid before Rewards were enabled, reward points should not be added
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function _isOrderPaidNow($order)
    {
        $isOrderPaid = (double)$order->getBaseTotalPaid() > 0 &&
            $order->getBaseGrandTotal() - $order->getBaseSubtotalCanceled() - $order->getBaseTotalPaid() < 0.0001;

        if (!$order->getOrigData('base_grand_total')) {
            //New order with "Sale" payment action
            return $isOrderPaid;
        }

        return $isOrderPaid && $order->getOrigData(
            'base_grand_total'
        ) - $order->getOrigData(
            'base_subtotal_canceled'
        ) - $order->getOrigData(
            'base_total_paid'
        ) >= 0.0001;
    }

    /**
     * Update invitation points balance after referral's order completed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    protected function _invitationToOrder($observer)
    {
        if ($this->_coreData->isModuleEnabled('Magento_Invitation')) {
            $invoice = $observer->getEvent()->getInvoice();
            /* @var $invoice \Magento\Sales\Model\Order\Invoice */
            $order = $invoice->getOrder();
            /* @var $order \Magento\Sales\Model\Order */
            if ($order->getBaseTotalDue() > 0) {
                return $this;
            }
            $invitation = $this->_invitationFactory->create()->load($order->getCustomerId(), 'referral_id');
            if (!$invitation->getId() || !$invitation->getCustomerId()) {
                return $this;
            }
            $this->_getRewardModel()->setActionEntity(
                $invitation
            )->setCustomerId(
                $invitation->getCustomerId()
            )->setStore(
                $order->getStoreId()
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_INVITATION_ORDER
            )->updateRewardPoints();
        }

        return $this;
    }

    /**
     * Set flag to reset reward points totals
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function quoteCollectTotalsBefore(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setRewardPointsTotalReseted(false);
        return $this;
    }

    /**
     * Set use reward points flag to new quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function quoteMergeAfter($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $source = $observer->getEvent()->getSource();

        if ($source->getUseRewardPoints()) {
            $quote->setUseRewardPoints($source->getUseRewardPoints());
        }
        return $this;
    }

    /**
     * Payment data import in checkout process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function paymentDataImport(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }
        $input = $observer->getEvent()->getInput();
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getPayment()->getQuote();
        $this->_paymentDataImport($quote, $input, $input->getUseRewardPoints());
        return $this;
    }

    /**
     * Enable Zero Subtotal Checkout payment method
     * if customer has enough points to cover grand total
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function preparePaymentMethod($observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }

        $quote = $observer->getEvent()->getQuote();
        if (!is_object($quote) || !$quote->getId()) {
            return $this;
        }

        /* @var $reward \Magento\Reward\Model\Reward */
        $reward = $quote->getRewardInstance();
        if (!$reward || !$reward->getId()) {
            return $this;
        }

        $baseQuoteGrandTotal = $quote->getBaseGrandTotal() + $quote->getBaseRewardCurrencyAmount();
        if ($reward->isEnoughPointsToCoverAmount($baseQuoteGrandTotal)) {
            $paymentCode = $observer->getEvent()->getMethodInstance()->getCode();
            $result = $observer->getEvent()->getResult();
            $result->isAvailable = $paymentCode === 'free' && empty($result->isDeniedInConfig);
        }
        return $this;
    }

    /**
     * Payment data import in admin order create process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function processOrderCreationData(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        if (!$this->_rewardData->isEnabledOnFront($quote->getStore()->getWebsiteId())) {
            return $this;
        }
        $request = $observer->getEvent()->getRequest();
        if (isset($request['payment']) && isset($request['payment']['use_reward_points'])) {
            $this->_paymentDataImport($quote, $quote->getPayment(), $request['payment']['use_reward_points']);
        }
        return $this;
    }

    /**
     * Prepare and set to quote reward balance instance,
     * set zero subtotal checkout payment if need
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Framework\Object $payment
     * @param bool $useRewardPoints
     * @return $this
     */
    protected function _paymentDataImport($quote, $payment, $useRewardPoints)
    {
        if (!$quote ||
            !$quote->getCustomerId() ||
            $quote->getBaseGrandTotal() + $quote->getBaseRewardCurrencyAmount() <= 0
        ) {
            return $this;
        }
        $quote->setUseRewardPoints((bool)$useRewardPoints);
        if ($quote->getUseRewardPoints()) {
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_getRewardModel()->setCustomer(
                $quote->getCustomer()
            )->setWebsiteId(
                $quote->getStore()->getWebsiteId()
            )->loadByCustomer();
            $minPointsBalance = (int)$this->_scopeConfig->getValue(
                \Magento\Reward\Model\Reward::XML_PATH_MIN_POINTS_BALANCE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $quote->getStoreId()
            );

            if ($reward->getId() && $reward->getPointsBalance() >= $minPointsBalance) {
                $quote->setRewardInstance($reward);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                }
            } else {
                $quote->setUseRewardPoints(false);
            }
        }
        return $this;
    }

    /**
     * Revert authorized reward points amount for order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    protected function _revertRewardPointsForOrder(\Magento\Sales\Model\Order $order)
    {
        if (!$order->getCustomerId()) {
            return $this;
        }
        $this->_getRewardModel()->setCustomerId(
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

    /**
     * Revert reward points if order was not placed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function revertRewardPoints(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        if ($order) {
            $this->_revertRewardPointsForOrder($order);
        }

        return $this;
    }

    /**
     * Revert authorized reward points amounts for all orders
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function revertRewardPointsForAllOrders(\Magento\Framework\Event\Observer $observer)
    {
        $orders = $observer->getEvent()->getOrders();

        foreach ($orders as $order) {
            $this->_revertRewardPointsForOrder($order);
        }

        return $this;
    }

    /**
     * Set forced can creditmemo flag if refunded amount less then invoiced amount of reward points
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function orderLoadAfter(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->canUnhold()) {
            return $this;
        }
        if ($order->isCanceled() || $order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED) {
            return $this;
        }
        if ($order->getBaseRwrdCrrncyAmtInvoiced() - $order->getBaseRwrdCrrncyAmntRefnded() > 0) {
            $order->setForcedCanCreditmemo(true);
        }
        return $this;
    }

    /**
     * Set invoiced reward amount to order after invoice register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function invoiceRegister(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $invoice \Magento\Sales\Model\Order\Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getBaseRewardCurrencyAmount()) {
            $order = $invoice->getOrder();
            $order->setRwrdCurrencyAmountInvoiced(
                $order->getRwrdCurrencyAmountInvoiced() + $invoice->getRewardCurrencyAmount()
            );
            $order->setBaseRwrdCrrncyAmtInvoiced(
                $order->getBaseRwrdCrrncyAmtInvoiced() + $invoice->getBaseRewardCurrencyAmount()
            );
        }

        return $this;
    }

    /**
     * Update inviter balance if possible
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function invoicePay(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $invoice \Magento\Sales\Model\Order\Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if (!$invoice->getOrigData($invoice->getResource()->getIdFieldName())) {
            $this->_invitationToOrder($observer);
        }

        return $this;
    }

    /**
     * Set reward points balance to refund before creditmemo register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function setRewardPointsBalanceToRefund(\Magento\Framework\Event\Observer $observer)
    {
        $input = $observer->getEvent()->getInput();
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (isset($input['refund_reward_points']) && isset($input['refund_reward_points_enable'])) {
            $enable = $input['refund_reward_points_enable'];
            $balance = (int)$input['refund_reward_points'];
            $balance = min($creditmemo->getRewardPointsBalance(), $balance);
            if ($enable && $balance) {
                $creditmemo->setRewardPointsBalanceRefund($balance);
            }
        }
        return $this;
    }

    /**
     * Clear forced can creditmemo if whole reward amount was refunded
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function creditmemoRefund(\Magento\Framework\Event\Observer $observer)
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

    /**
     * Send scheduled low balance warning notifications
     *
     * @return $this
     */
    public function scheduledBalanceExpireNotification()
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }

        foreach ($this->_storeManager->getWebsites() as $website) {
            if (!$this->_rewardData->isEnabledOnFront($website->getId())) {
                continue;
            }
            $inDays = (int)$this->_rewardData->getNotificationConfig('expiry_day_before');
            if (!$inDays) {
                continue;
            }
            $collection = $this->_historyCollectionFactory->create()->setExpiryConfig(
                $this->_rewardData->getExpiryConfig()
            )->loadExpiredSoonPoints(
                $website->getId(),
                true
            )->addNotificationSentFlag(
                false
            )->addCustomerInfo()->setPageSize(
                20
            )->setCurPage(
                1
            )->load();

            foreach ($collection as $item) {
                $this->_getRewardModel()->sendBalanceWarningNotification($item, $website->getId());
            }

            // mark records as sent
            $historyIds = $collection->getExpiredSoonIds();
            $this->_historyItemFactory->create()->markAsNotified($historyIds);
        }

        return $this;
    }

    /**
     * Make points expired
     *
     * @return $this
     */
    public function scheduledPointsExpiration()
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        foreach ($this->_storeManager->getWebsites() as $website) {
            if (!$this->_rewardData->isEnabledOnFront($website->getId())) {
                continue;
            }
            $expiryType = $this->_rewardData->getGeneralConfig('expiry_calculation', $website->getId());
            $this->_historyItemFactory->create()->expirePoints($website->getId(), $expiryType, 100);
        }

        return $this;
    }

    /**
     * Prepare orphan points of customers after website was deleted
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function prepareCustomerOrphanPoints(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $website \Magento\Store\Model\Website */
        $website = $observer->getEvent()->getWebsite();
        $this->_getRewardModel()->prepareOrphanPoints($website->getId(), $website->getBaseCurrencyCode());
        return $this;
    }

    /**
     * Prepare salesrule form. Add field to specify reward points delta
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function prepareSalesruleForm(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('action_fieldset');
        $fieldset->addField(
            'reward_points_delta',
            'text',
            array(
                'name' => 'reward_points_delta',
                'label' => __('Add Reward Points'),
                'title' => __('Add Reward Points')
            ),
            'stop_rules_processing'
        );
        return $this;
    }

    /**
     * Set reward points delta to salesrule model after it loaded
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function loadRewardSalesruleData(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        /* @var $salesRule \Magento\SalesRule\Model\Rule */
        $salesRule = $observer->getEvent()->getRule();
        if ($salesRule->getId()) {
            $data = $this->_rewardResourceFactory->create()->getRewardSalesrule($salesRule->getId());
            if (isset($data['points_delta'])) {
                $salesRule->setRewardPointsDelta($data['points_delta']);
            }
        }
        return $this;
    }

    /**
     * Save reward points delta for salesrule
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function saveRewardSalesruleData(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        /* @var $salesRule \Magento\SalesRule\Model\Rule */
        $salesRule = $observer->getEvent()->getRule();
        $this->_rewardResourceFactory->create()->saveRewardSalesrule(
            $salesRule->getId(),
            (int)$salesRule->getRewardPointsDelta()
        );
        return $this;
    }

    /**
     * Update customer reward points balance by points from applied sales rules
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function applyRewardSalesrulePoints(\Magento\Framework\Event\Observer $observer)
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
            $reward = $this->_getRewardModel()->setCustomerId(
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

    /**
     * If not all rates found, we should disable reward points on frontend
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function checkRates(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }

        $groupId = $observer->getEvent()->getCustomerSession()->getCustomerGroupId();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        $rate = $this->_rateFactory->create();

        $hasRates = $rate->fetch(
            $groupId,
            $websiteId,
            \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
        )->getId() && $rate->reset()->fetch(
            $groupId,
            $websiteId,
            \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
        )->getId();

        $this->_rewardData->setHasRates($hasRates);

        return $this;
    }

    /**
     * Add reward amount to payment discount total
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addPaymentRewardItem(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $salesEntity = $cart->getSalesModel();
        $discount = abs($salesEntity->getDataUsingMethod('base_reward_currency_amount'));
        if ($discount > 0.0001) {
            $cart->addDiscount((double)$discount);
        }
    }

    /**
     * Return reward points
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function returnRewardPoints(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getRewardPointsBalance() > 0) {
            $this->_getRewardModel()->setCustomerId(
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
        }

        return $this;
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
