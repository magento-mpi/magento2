<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model;

use Magento\Framework\Model\Exception;

/**
 * Customer balance observer
 */
class Observer
{
    /**
     * Customer balance data
     *
     * @var \Magento\CustomerBalance\Helper\Data
     */
    protected $_customerBalanceData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $_onePageCheckout;

    /** @var \Magento\Customer\Model\Converter */
    protected $_customerConverter;

    /**
     * @param \Magento\Checkout\Model\Type\Onepage $onePageCheckout
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\CustomerBalance\Helper\Data $customerBalanceData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Converter $customerConverter
     */
    public function __construct(
        \Magento\Checkout\Model\Type\Onepage $onePageCheckout,
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\CustomerBalance\Helper\Data $customerBalanceData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Converter $customerConverter
    ) {
        $this->_onePageCheckout = $onePageCheckout;
        $this->_balanceFactory = $balanceFactory;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_customerBalanceData = $customerBalanceData;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerConverter = $customerConverter;
    }

    /**
     * Customer balance update after save
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function customerSaveAfter($observer)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return;
        }
        /* @var $request \Magento\Framework\App\RequestInterface */
        $request = $observer->getRequest();
        $data = $request->getPost('customerbalance');
        /* @var $customer \Magento\Customer\Service\V1\Data\Customer */
        $customer = $observer->getCustomer();
        $customerModel = $this->_customerConverter->getCustomerModel($customer->getId());
        if ($data) {
            if (!empty($data['amount_delta'])) {
                $balance = $this->_balanceFactory->create()->setCustomer(
                    $customerModel
                )->setWebsiteId(
                    isset($data['website_id']) ? $data['website_id'] : $customer->getWebsiteId()
                )->setAmountDelta(
                    $data['amount_delta']
                )->setComment(
                    $data['comment']
                );
                if (isset($data['notify_by_email'])) {
                    if (isset($data['store_id'])) {
                        $balance->setNotifyByEmail(true, $data['store_id']);
                    } elseif ($this->_storeManager->isSingleStoreMode()) {
                        $stores = $this->_storeManager->getStores();
                        $singleStore = array_shift($stores);
                        $balance->setNotifyByEmail(true, $singleStore->getId());
                    }
                }
                $balance->save();
            }
        }
    }

    /**
     * Check for customer balance use switch & update payment info
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function paymentDataImport(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return;
        }

        $input = $observer->getEvent()->getInput();
        $payment = $observer->getEvent()->getPayment();
        $this->_importPaymentData($payment->getQuote(), $input, $input->getUseCustomerBalance());
    }

    /**
     * Check store credit balance
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  $this
     * @throws  Exception
     */
    protected function _checkStoreCreditBalance(\Magento\Sales\Model\Order $order)
    {
        if ($order->getBaseCustomerBalanceAmount() > 0) {
            $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();

            $balance = $this->_balanceFactory->create()->setCustomerId(
                $order->getCustomerId()
            )->setWebsiteId(
                $websiteId
            )->loadByCustomer()->getAmount();

            if ($order->getBaseCustomerBalanceAmount() - $balance >= 0.0001) {
                $this->_onePageCheckout->create()->getCheckout()->setUpdateSection(
                    'payment-method'
                )->setGotoSection(
                    'payment'
                );

                throw new Exception(__('You do not have enough store credit to complete this order.'));
            }
        }

        return $this;
    }

    /**
     * Validate balance just before placing an order
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function processBeforeOrderPlace(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_customerBalanceData->isEnabled()) {
            $order = $observer->getEvent()->getOrder();
            $this->_checkStoreCreditBalance($order);
        }

        return $this;
    }

    /**
     * Check if customer balance was used in quote and reduce balance if so
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function processOrderPlace(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order->getBaseCustomerBalanceAmount() > 0) {
            $this->_checkStoreCreditBalance($order);

            $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();
            $this->_balanceFactory->create()->setCustomerId(
                $order->getCustomerId()
            )->setWebsiteId(
                $websiteId
            )->setAmountDelta(
                -$order->getBaseCustomerBalanceAmount()
            )->setHistoryAction(
                \Magento\CustomerBalance\Model\Balance\History::ACTION_USED
            )->setOrder(
                $order
            )->save();
        }

        return $this;
    }

    /**
     * Revert authorized store credit amount for order
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  $this
     */
    protected function _revertStoreCreditForOrder(\Magento\Sales\Model\Order $order)
    {
        if (!$order->getCustomerId() || !$order->getBaseCustomerBalanceAmount()) {
            return $this;
        }

        $this->_balanceFactory->create()->setCustomerId(
            $order->getCustomerId()
        )->setWebsiteId(
            $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
        )->setAmountDelta(
            $order->getBaseCustomerBalanceAmount()
        )->setHistoryAction(
            \Magento\CustomerBalance\Model\Balance\History::ACTION_REVERTED
        )->setOrder(
            $order
        )->save();

        return $this;
    }

    /**
     * Revert store credit if order was not placed
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function revertStoreCredit(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        if ($order) {
            $this->_revertStoreCreditForOrder($order);
        }

        return $this;
    }

    /**
     * Revert authorized store credit amounts for all orders
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function revertStoreCreditForAllOrders(\Magento\Framework\Event\Observer $observer)
    {
        $orders = $observer->getEvent()->getOrders();

        foreach ($orders as $order) {
            $this->_revertStoreCreditForOrder($order);
        }

        return $this;
    }

    /**
     * The same as paymentDataImport(), but for admin checkout
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function processOrderCreationData(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return $this;
        }
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();
        if (isset($request['payment']) && isset($request['payment']['use_customer_balance'])) {
            $this->_importPaymentData(
                $quote,
                $quote->getPayment(),
                (bool)(int)$request['payment']['use_customer_balance']
            );
        }
    }

    /**
     * Analyze payment data for quote and set free shipping if grand total is covered by balance
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Framework\Object|\Magento\Sales\Model\Quote\Payment $payment
     * @param bool $shouldUseBalance
     * @return void
     */
    protected function _importPaymentData($quote, $payment, $shouldUseBalance)
    {
        $store = $this->_storeManager->getStore($quote->getStoreId());
        if (!$quote ||
            !$quote->getCustomerId() ||
            $quote->getBaseGrandTotal() + $quote->getBaseCustomerBalanceAmountUsed() <= 0
        ) {
            return;
        }
        $quote->setUseCustomerBalance($shouldUseBalance);
        if ($shouldUseBalance) {
            $balance = $this->_balanceFactory->create()->setCustomerId(
                $quote->getCustomerId()
            )->setWebsiteId(
                $store->getWebsiteId()
            )->loadByCustomer();
            if ($balance) {
                $quote->setCustomerBalanceInstance($balance);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                }
            } else {
                $quote->setUseCustomerBalance(false);
            }
        }
    }

    /**
     * Make only Zero Subtotal Checkout enabled if SC covers entire balance
     *
     * The Customerbalance instance must already be in the quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function togglePaymentMethods($observer)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return;
        }

        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        $balance = $quote->getCustomerBalanceInstance();
        if (!$balance) {
            return;
        }

        // disable all payment methods and enable only Zero Subtotal Checkout
        if ($balance->isFullAmountCovered($quote)) {
            $paymentMethod = $observer->getEvent()->getMethodInstance()->getCode();
            $result = $observer->getEvent()->getResult();
            $result->isAvailable = $paymentMethod === 'free' && empty($result->isDeniedInConfig);
        }
    }

    /**
     * Set the flag that we need to collect overall totals
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function quoteCollectTotalsBefore(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setCustomerBalanceCollected(false);
    }

    /**
     * Set the source customer balance usage flag into new quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function quoteMergeAfter(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $source = $observer->getEvent()->getSource();

        if ($source->getUseCustomerBalance()) {
            $quote->setUseCustomerBalance($source->getUseCustomerBalance());
        }
    }

    /**
     * Increase order customer_balance_invoiced attribute based on created invoice
     * used for event: sales_order_invoice_save_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function increaseOrderInvoicedAmount(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        /**
         * Update customer balance only if invoice is just created
         */
        if ($invoice->getOrigData() === null && $invoice->getBaseCustomerBalanceAmount()) {
            $order->setBaseCustomerBalanceInvoiced(
                $order->getBaseCustomerBalanceInvoiced() + $invoice->getBaseCustomerBalanceAmount()
            );
            $order->setCustomerBalanceInvoiced(
                $order->getCustomerBalanceInvoiced() + $invoice->getCustomerBalanceAmount()
            );
        }
        /**
         * Because of order doesn't save second time, added forced saving below attributes
         */
        $order->getResource()->saveAttribute($order, 'base_customer_balance_invoiced');
        $order->getResource()->saveAttribute($order, 'customer_balance_invoiced');
        return $this;
    }

    /**
     * Refund process
     * used for event: sales_order_creditmemo_save_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws Exception
     */
    public function creditmemoSaveAfter(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getAutomaticallyCreated()) {
            if ($this->_customerBalanceData->isAutoRefundEnabled()) {
                $creditmemo->setCustomerBalanceRefundFlag(
                    true
                )->setCustomerBalTotalRefunded(
                    $creditmemo->getCustomerBalanceAmount()
                )->setBsCustomerBalTotalRefunded(
                    $creditmemo->getBaseCustomerBalanceAmount()
                );
            } else {
                return $this;
            }
        }
        $customerBalanceReturnMax = $creditmemo->getCustomerBalanceReturnMax() ===
            null ? 0 : $creditmemo->getCustomerBalanceReturnMax();

        if ((double)(string)$creditmemo->getCustomerBalTotalRefunded() > (double)(string)$customerBalanceReturnMax) {
            throw new Exception(__('The store credit used cannot exceed order amount.'));
        }
        //doing actual refund to customer balance if user have submitted refund form
        if ($creditmemo->getCustomerBalanceRefundFlag() && $creditmemo->getBsCustomerBalTotalRefunded()) {
            $order->setBsCustomerBalTotalRefunded(
                $order->getBsCustomerBalTotalRefunded() + $creditmemo->getBsCustomerBalTotalRefunded()
            );
            $order->setCustomerBalTotalRefunded(
                $order->getCustomerBalTotalRefunded() + $creditmemo->getCustomerBalTotalRefunded()
            );

            $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();

            $this->_balanceFactory->create()->setCustomerId(
                $order->getCustomerId()
            )->setWebsiteId(
                $websiteId
            )->setAmountDelta(
                $creditmemo->getBsCustomerBalTotalRefunded()
            )->setHistoryAction(
                \Magento\CustomerBalance\Model\Balance\History::ACTION_REFUNDED
            )->setOrder(
                $order
            )->setCreditMemo(
                $creditmemo
            )->save();
        }

        return $this;
    }

    /**
     * Set refund flag to creditmemo based on user input
     * used for event: adminhtml_sales_order_creditmemo_register_before
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function creditmemoDataImport(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $input = $observer->getEvent()->getInput();

        if (isset($input['refund_customerbalance_return']) && isset($input['refund_customerbalance_return_enable'])) {
            $enable = $input['refund_customerbalance_return_enable'];
            $amount = $input['refund_customerbalance_return'];
            if ($enable && is_numeric($amount)) {
                $amount = max(0, min($creditmemo->getBaseCustomerBalanceReturnMax(), $amount));
                if ($amount) {
                    $amount = $creditmemo->getStore()->roundPrice($amount);
                    $creditmemo->setBsCustomerBalTotalRefunded($amount);

                    $amount = $creditmemo->getStore()->roundPrice(
                        $amount * $creditmemo->getOrder()->getBaseToOrderRate()
                    );
                    $creditmemo->setCustomerBalTotalRefunded($amount);
                    //setting flag to make actual refund to customer balance after creditmemo save
                    $creditmemo->setCustomerBalanceRefundFlag(true);
                    //allow online refund
                    $creditmemo->setPaymentRefundDisallowed(false);
                }
            }
        }

        if (isset($input['refund_customerbalance']) && $input['refund_customerbalance']) {
            $creditmemo->setRefundCustomerBalance(true);
        }

        if (isset($input['refund_real_customerbalance']) && $input['refund_real_customerbalance']) {
            $creditmemo->setRefundRealCustomerBalance(true);
            $creditmemo->setPaymentRefundDisallowed(true);
        }

        return $this;
    }

    /**
     * Set forced canCreditmemo flag
     * used for event: sales_order_load_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesOrderLoadAfter(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->isCanceled() || $order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED) {
            return $this;
        }

        if ($order->getCustomerBalanceInvoiced() - $order->getCustomerBalanceRefunded() > 0) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }

    /**
     * Set refund amount to creditmemo
     * used for event: sales_order_creditmemo_refund
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function refund(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();


        if ($creditmemo->getRefundRealCustomerBalance() && $creditmemo->getBaseGrandTotal()) {
            $baseAmount = $creditmemo->getBaseGrandTotal();
            $amount = $creditmemo->getGrandTotal();

            $creditmemo->setBsCustomerBalTotalRefunded($creditmemo->getBsCustomerBalTotalRefunded() + $baseAmount);
            $creditmemo->setCustomerBalTotalRefunded($creditmemo->getCustomerBalTotalRefunded() + $amount);
        }

        if ($creditmemo->getBaseCustomerBalanceAmount()) {
            if ($creditmemo->getRefundCustomerBalance()) {
                $baseAmount = $creditmemo->getBaseCustomerBalanceAmount();
                $amount = $creditmemo->getCustomerBalanceAmount();

                $creditmemo->setBsCustomerBalTotalRefunded($creditmemo->getBsCustomerBalTotalRefunded() + $baseAmount);
                $creditmemo->setCustomerBalTotalRefunded($creditmemo->getCustomerBalTotalRefunded() + $amount);
            }

            $order->setBaseCustomerBalanceRefunded(
                $order->getBaseCustomerBalanceRefunded() + $creditmemo->getBaseCustomerBalanceAmount()
            );
            $order->setCustomerBalanceRefunded(
                $order->getCustomerBalanceRefunded() + $creditmemo->getCustomerBalanceAmount()
            );

            // we need to update flag after credit memo was refunded and order's properties changed
            if ($order->getCustomerBalanceInvoiced() > 0 &&
                $order->getCustomerBalanceInvoiced() == $order->getCustomerBalanceRefunded()
            ) {
                $order->setForcedCanCreditmemo(false);
            }
        }

        return $this;
    }

    /**
     * Defined in Logging/etc/logging.xml - special handler for setting second action for customerBalance change
     *
     * @param string $action
     * @return void
     */
    public function predispatchPrepareLogging($action)
    {
        $data = $this->_request->getParam('customerbalance');
        if (isset($data['amount_delta']) && $data['amount_delta'] != '') {
            $actions = $this->_coreRegistry->registry('magento_logged_actions');
            if (!is_array($actions)) {
                $actions = array($actions);
            }
            $actions[] = 'adminhtml_customerbalance_save';
            $this->_coreRegistry->unregister('magento_logged_actions');
            $this->_coreRegistry->register('magento_logged_actions', $actions);
        }
    }

    /**
     * Set customers balance currency code to website base currency code on website deletion
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function setCustomersBalanceCurrencyToWebsiteBaseCurrency(\Magento\Framework\Event\Observer $observer)
    {
        $this->_balanceFactory->create()->setCustomersBalanceCurrencyTo(
            $observer->getEvent()->getWebsite()->getWebsiteId(),
            $observer->getEvent()->getWebsite()->getBaseCurrencyCode()
        );
        return $this;
    }

    /**
     * Add customer balance amount to payment discount total
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addPaymentCustomerBalanceItem(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $cart \Magento\Payment\Model\Cart */
        $cart = $observer->getEvent()->getCart();
        $salesEntity = $cart->getSalesModel();
        $value = abs($salesEntity->getDataUsingMethod('customer_balance_base_amount'));
        if ($value > 0.0001) {
            $cart->addDiscount((double)$value);
        }
    }

    /**
     * Extend sales amount expression with customer balance refunded value
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function extendSalesAmountExpression(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $expressionTransferObject \Magento\Framework\Object */
        $expressionTransferObject = $observer->getEvent()->getExpressionObject();
        /** @var $adapter \Magento\Framework\DB\Adapter\AdapterInterface */
        $adapter = $observer->getEvent()->getCollection()->getConnection();
        $expressionTransferObject->setExpression($expressionTransferObject->getExpression() . ' + (%s)');
        $arguments = $expressionTransferObject->getArguments();
        $arguments[] = $adapter->getCheckSql(
            $adapter->prepareSqlCondition('main_table.bs_customer_bal_total_refunded', array('null' => null)),
            0,
            sprintf(
                'main_table.bs_customer_bal_total_refunded - %s - %s',
                $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                $adapter->getIfNullSql('main_table.base_shipping_refunded', 0)
            )
        );

        $expressionTransferObject->setArguments($arguments);
    }

    /**
     * Modify the amount of invoiced funds for which reward points should not be voided after refund.
     * Prevent voiding of reward points for amount returned to store credit.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function modifyRewardedAmountOnRefund(\Magento\Framework\Event\Observer $observer)
    {
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $order = $creditMemo->getOrder();

        $rewardedAmountAfterRefund = $creditMemo->getRewardedAmountAfterRefund();

        $customerBalanceTotalRefunded = $order->getBaseCustomerBalanceTotalRefunded();
        $rewardedAmountRefunded = $order->getBaseTotalRefunded() - $order->getBaseTaxRefunded()
            - $order->getBaseShippingRefunded();
        if ($customerBalanceTotalRefunded > $rewardedAmountRefunded) {
            $rewardedAmountAfterRefund += $rewardedAmountRefunded;
        } else {
             $rewardedAmountAfterRefund += $customerBalanceTotalRefunded;
        }

        $creditMemo->setRewardedAmountAfterRefund($rewardedAmountAfterRefund);
    }
}
