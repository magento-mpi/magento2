<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance observer
 */
class Magento_CustomerBalance_Model_Observer
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Prepare customer balance POST data
     *
     * @param Magento_Event_Observer $observer
     */
    public function prepareCustomerBalanceSave($observer)
    {
        if (!Mage::helper('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
            return;
        }
        /* @var $customer Magento_Customer_Model_Customer */
        $customer = $observer->getCustomer();
        /* @var $request Magento_Core_Controller_Request_Http */
        $request = $observer->getRequest();
        $data = $request->getPost('customerbalance');
        if ($data) {
            $customer->setCustomerBalanceData($data);
        }
    }

    /**
     * Customer balance update after save
     *
     * @param Magento_Event_Observer $observer
     */
    public function customerSaveAfter($observer)
    {
        if (!Mage::helper('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
            return;
        }
        $data = $observer->getCustomer()->getCustomerBalanceData();
        if ($data) {
            if (!empty($data['amount_delta'])) {
                $balance = Mage::getModel('Magento_CustomerBalance_Model_Balance')
                    ->setCustomer($observer->getCustomer())
                    ->setWebsiteId(
                        isset($data['website_id']) ? $data['website_id'] : $observer->getCustomer()->getWebsiteId()
                    )
                    ->setAmountDelta($data['amount_delta'])
                    ->setComment($data['comment']);
                if (isset($data['notify_by_email'])) {
                    if (isset($data['store_id'])) {
                        $balance->setNotifyByEmail(true, $data['store_id']);
                    } elseif (Mage::app()->isSingleStoreMode()) {
                        $stores = Mage::app()->getStores();
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
     * @param Magento_Event_Observer $observer
     */
    public function paymentDataImport(Magento_Event_Observer $observer)
    {
        if (!Mage::helper('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
            return;
        }

        $input = $observer->getEvent()->getInput();
        $payment = $observer->getEvent()->getPayment();
        $this->_importPaymentData($payment->getQuote(), $input, $input->getUseCustomerBalance());
    }

    /**
     * Check store credit balance
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_CustomerBalance_Model_Observer
     */
    protected function _checkStoreCreditBalance(Magento_Sales_Model_Order $order)
    {
        if ($order->getBaseCustomerBalanceAmount() > 0) {
            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();

            $balance = Mage::getModel('Magento_CustomerBalance_Model_Balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->loadByCustomer()
                ->getAmount();

            if (($order->getBaseCustomerBalanceAmount() - $balance) >= 0.0001) {
                Mage::getSingleton('Magento_Checkout_Model_Type_Onepage')
                    ->getCheckout()
                    ->setUpdateSection('payment-method')
                    ->setGotoSection('payment');

                Mage::throwException(__('You do not have enough store credit to complete this order.'));
            }
        }

        return $this;
    }

    /**
     * Validate balance just before placing an order
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CustomerBalance_Model_Observer
     */
    public function processBeforeOrderPlace(Magento_Event_Observer $observer)
    {
        if (Mage::helper('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
            $order = $observer->getEvent()->getOrder();
            $this->_checkStoreCreditBalance($order);
        }

        return $this;
    }

    /**
     * Check if customer balance was used in quote and reduce balance if so
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CustomerBalance_Model_Observer
     */
    public function processOrderPlace(Magento_Event_Observer $observer)
    {
        if (!Mage::helper('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order->getBaseCustomerBalanceAmount() > 0) {
            $this->_checkStoreCreditBalance($order);

            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();
            Mage::getModel('Magento_CustomerBalance_Model_Balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->setAmountDelta(-$order->getBaseCustomerBalanceAmount())
                ->setHistoryAction(Magento_CustomerBalance_Model_Balance_History::ACTION_USED)
                ->setOrder($order)
                ->save();
        }

        return $this;
    }

    /**
     * Revert authorized store credit amount for order
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_CustomerBalance_Model_Observer
     */
    protected function _revertStoreCreditForOrder(Magento_Sales_Model_Order $order)
    {
        if (!$order->getCustomerId() || !$order->getBaseCustomerBalanceAmount()) {
            return $this;
        }

        Mage::getModel('Magento_CustomerBalance_Model_Balance')
            ->setCustomerId($order->getCustomerId())
            ->setWebsiteId(Mage::app()->getStore($order->getStoreId())->getWebsiteId())
            ->setAmountDelta($order->getBaseCustomerBalanceAmount())
            ->setHistoryAction(Magento_CustomerBalance_Model_Balance_History::ACTION_REVERTED)
            ->setOrder($order)
            ->save();

        return $this;
    }

    /**
     * Revert store credit if order was not placed
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CustomerBalance_Model_Observer
     */
    public function revertStoreCredit(Magento_Event_Observer $observer)
    {
        /* @var $order Magento_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        if ($order) {
            $this->_revertStoreCreditForOrder($order);
        }

        return $this;
    }

    /**
     * Revert authorized store credit amounts for all orders
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CustomerBalance_Model_Observer
     */
    public function revertStoreCreditForAllOrders(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return $this
     */
    public function processOrderCreationData(Magento_Event_Observer $observer)
    {
        if (!Mage::helper('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
            return $this;
        }
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();
        if (isset($request['payment']) && isset($request['payment']['use_customer_balance'])) {
            $this->_importPaymentData($quote, $quote->getPayment(),
                (bool)(int)$request['payment']['use_customer_balance']);
        }
    }

    /**
     * Analyze payment data for quote and set free shipping if grand total is covered by balance
     *
     * @param Magento_Sales_Model_Quote $quote
     * @param Magento_Object|Magento_Sales_Model_Quote_Payment $payment
     * @param bool $shouldUseBalance
     */
    protected function _importPaymentData($quote, $payment, $shouldUseBalance)
    {
        $store = Mage::app()->getStore($quote->getStoreId());
        if (!$quote || !$quote->getCustomerId()
            || $quote->getBaseGrandTotal() + $quote->getBaseCustomerBalanceAmountUsed() <= 0
        ) {
            return;
        }
        $quote->setUseCustomerBalance($shouldUseBalance);
        if ($shouldUseBalance) {
            $balance = Mage::getModel('Magento_CustomerBalance_Model_Balance')
                ->setCustomerId($quote->getCustomerId())
                ->setWebsiteId($store->getWebsiteId())
                ->loadByCustomer();
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
     * @param Magento_Event_Observer $observer
     */
    public function togglePaymentMethods($observer)
    {
        if (!Mage::helper('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
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
     * @param Magento_Event_Observer $observer
     */
    public function quoteCollectTotalsBefore(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setCustomerBalanceCollected(false);
    }

    /**
     * Set the source customer balance usage flag into new quote
     *
     * @param Magento_Event_Observer $observer
     */
    public function quoteMergeAfter(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerBalance_Model_Observer
     */
    public function increaseOrderInvoicedAmount(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerBalance_Model_Observer
     */
    public function creditmemoSaveAfter(Magento_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getAutomaticallyCreated()) {
            if (Mage::helper('Magento_CustomerBalance_Helper_Data')->isAutoRefundEnabled()) {
                $creditmemo->setCustomerBalanceRefundFlag(true)
                    ->setCustomerBalTotalRefunded($creditmemo->getCustomerBalanceAmount())
                    ->setBsCustomerBalTotalRefunded($creditmemo->getBaseCustomerBalanceAmount());
            } else {
                return $this;
            }
        }
        $customerBalanceReturnMax = ($creditmemo->getCustomerBalanceReturnMax() === null) ? 0 :
            $creditmemo->getCustomerBalanceReturnMax();

        if ((float)(string)$creditmemo->getCustomerBalTotalRefunded() > (float)(string)$customerBalanceReturnMax) {
            Mage::throwException(__('The store credit used cannot exceed order amount.'));
        }
        //doing actual refund to customer balance if user have submitted refund form
        if ($creditmemo->getCustomerBalanceRefundFlag() && $creditmemo->getBsCustomerBalTotalRefunded()) {
            $order->setBsCustomerBalTotalRefunded(
                $order->getBsCustomerBalTotalRefunded() + $creditmemo->getBsCustomerBalTotalRefunded()
            );
            $order->setCustomerBalTotalRefunded(
                $order->getCustomerBalTotalRefunded() + $creditmemo->getCustomerBalTotalRefunded()
            );

            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();

            Mage::getModel('Magento_CustomerBalance_Model_Balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->setAmountDelta($creditmemo->getBsCustomerBalTotalRefunded())
                ->setHistoryAction(Magento_CustomerBalance_Model_Balance_History::ACTION_REFUNDED)
                ->setOrder($order)
                ->setCreditMemo($creditmemo)
                ->save();
        }

        return $this;
    }

    /**
     * Set refund flag to creditmemo based on user input
     * used for event: adminhtml_sales_order_creditmemo_register_before
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerBalance_Model_Observer
     */
    public function creditmemoDataImport(Magento_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $input = $request->getParam('creditmemo');

        if (isset($input['refund_customerbalance_return']) && isset($input['refund_customerbalance_return_enable'])) {
            $enable = $input['refund_customerbalance_return_enable'];
            $amount = $input['refund_customerbalance_return'];
            if ($enable && is_numeric($amount)) {
                $amount = max(0, min($creditmemo->getBaseCustomerBalanceReturnMax(), $amount));
                if ($amount) {
                    $amount = $creditmemo->getStore()->roundPrice($amount);
                    $creditmemo->setBsCustomerBalTotalRefunded($amount);

                    $amount = $creditmemo->getStore()->roundPrice(
                        $amount*$creditmemo->getOrder()->getBaseToOrderRate()
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerBalance_Model_Observer
     */
    public function salesOrderLoadAfter(Magento_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->isCanceled() ||
            $order->getState() === Magento_Sales_Model_Order::STATE_CLOSED ) {
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerBalance_Model_Observer
     */
    public function refund(Magento_Event_Observer $observer)
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
            if ($order->getCustomerBalanceInvoiced() > 0
                && $order->getCustomerBalanceInvoiced() == $order->getCustomerBalanceRefunded()
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
     */
    public function predispatchPrepareLogging($action)
    {
        $request = Mage::app()->getRequest();
        $data = $request->getParam('customerbalance');
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerBalance_Model_Observer
     */
    public function setCustomersBalanceCurrencyToWebsiteBaseCurrency(Magento_Event_Observer $observer)
    {
        Mage::getModel('Magento_CustomerBalance_Model_Balance')->setCustomersBalanceCurrencyTo(
            $observer->getEvent()->getWebsite()->getWebsiteId(),
            $observer->getEvent()->getWebsite()->getBaseCurrencyCode()
        );
        return $this;
    }

    /**
     * Add customer balance amount to PayPal discount total
     *
     * @param Magento_Event_Observer $observer
     */
    public function addPaypalCustomerBalanceItem(Magento_Event_Observer $observer)
    {
        $paypalCart = $observer->getEvent()->getPaypalCart();
        if ($paypalCart) {
            $salesEntity = $paypalCart->getSalesEntity();
            if ($salesEntity instanceof Magento_Sales_Model_Quote) {
                $balanceField = 'base_customer_bal_amount_used';
            } elseif ($salesEntity instanceof Magento_Sales_Model_Order) {
                $balanceField = 'base_customer_balance_amount';
            } else {
                return;
            }

            $value = abs($salesEntity->getDataUsingMethod($balanceField));
            if ($value > 0.0001) {
                $paypalCart->updateTotal(Magento_Paypal_Model_Cart::TOTAL_DISCOUNT, (float)$value,
                    __('Store Credit (%1)', Mage::app()->getStore()->convertPrice($value, true, false))
                );
            }
        }
    }

    /**
     * Extend sales amount expression with customer balance refunded value
     *
     * @param Magento_Event_Observer $observer
     */
    public function extendSalesAmountExpression(Magento_Event_Observer $observer)
    {
        /** @var $expressionTransferObject Magento_Object */
        $expressionTransferObject = $observer->getEvent()->getExpressionObject();
        /** @var $adapter Magento_DB_Adapter_Interface */
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
}
