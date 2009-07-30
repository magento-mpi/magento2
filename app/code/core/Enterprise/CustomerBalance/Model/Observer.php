<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer balance observer
 *
 */
class Enterprise_CustomerBalance_Model_Observer
{
    /**
     * Prepare customer balance POST data
     *
     * @param Varien_Event_Observer $observer
     */
    public function prepareCustomerBalanceSave($observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getCustomer();
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getRequest();
        if ($data = $request->getPost('customerbalance')) {
            $customer->setCustomerBalanceData($data);
        }
    }

    /**
     * Customer balance update after save
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerSaveAfter($observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }
        if ($data = $observer->getCustomer()->getCustomerBalanceData()) {
            if (!empty($data['amount_delta'])) {
                $balance = Mage::getModel('enterprise_customerbalance/balance')
                    ->setCustomer($observer->getCustomer())
                    ->setWebsiteId(isset($data['website_id']) ? $data['website_id'] : $observer->getCustomer()->getWebsiteId())
                    ->setAmountDelta($data['amount_delta'])
                    ->setComment($data['comment'])
                ;
                if (isset($data['notify_by_email']) && isset($data['store_id'])) {
                    $balance->setNotifyByEmail(true, $data['store_id']);
                }
                $balance->save();
            }
        }
    }

    /**
     * Check for customer balance use switch & update payment info
     *
     * @param Varien_Event_Observer $observer
     */
    public function paymentDataImport(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }
        $input = $observer->getEvent()->getInput();
        $payment = $observer->getEvent()->getPayment();
        $quote = $payment->getQuote();

        if (!$quote->getCustomerId()) {
            return;
        }

        $store = Mage::app()->getStore($quote->getStoreId());

        $balance = Mage::getModel('enterprise_customerbalance/balance')
            ->setCustomerId($quote->getCustomerId())
            ->setWebsiteId($store->getWebsiteId())
            ->loadByCustomer()
            ->getAmount();

        if ($input->getUseCustomerBalance() && $balance < $quote->getBaseCustomerBalanceAmountUsed()) {
            Mage::throwException(Mage::helper('enterprise_customerbalance')->__("You don't have enough store credit to complete this order."));

        }

        $total = $quote->getBaseGrandTotal()+$quote->getBaseCustomerBalanceAmountUsed();

        $quote->setUseCustomerBalance($input->getUseCustomerBalance());
        if ($input->getUseCustomerBalance() && $balance >= $total) {
            $input->setMethod('free');
        }
    }

    /**
     * Check customer balance used in order
     *
     * @param Varien_Event_Observer $observer
     */
    public function processBeforeOrderPlace(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order->getBaseCustomerBalanceAmount() > 0) {
            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();

            $balance = Mage::getModel('enterprise_customerbalance/balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->loadByCustomer()
                ->getAmount();

            if (($order->getBaseCustomerBalanceAmount() - $balance) >= 0.0001) {
                Mage::getSingleton('checkout/type_onepage')
                    ->getCheckout()
                    ->setUpdateSection('payment-method')
                    ->setGotoSection('payment');

                Mage::throwException(Mage::helper('enterprise_customerbalance')->__("You don't have enough store credit to complete this order."));
            }
        }
    }

    /**
     * Check if customer balance was used in quote and reduce balance if so
     *
     * @param Varien_Event_Observer $observer
     */
    public function processOrderPlace(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order->getBaseCustomerBalanceAmount() > 0) {
            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();
            $balance = Mage::getModel('enterprise_customerbalance/balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->setAmountDelta(-$order->getBaseCustomerBalanceAmount())
                ->setHistoryAction(Enterprise_CustomerBalance_Model_Balance_History::ACTION_USED)
                ->setOrder($order)
                ->save();
        }
    }

    /**
     * Disable entire customerbalance layout
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableLayout($observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            unset($observer->getUpdates()->enterprise_customerbalance);
        }
    }

    /**
     * Process post data and set usage of customer balance into order creation model
     *
     * @param Varien_Event_Observer $observer
     */
    public function processOrderCreationData(Varien_Event_Observer $observer)
    {
        $model = $observer->getEvent()->getOrderCreateModel();
        $request = $observer->getEvent()->getRequest();
        $quote = $model->getQuote();
        $payment = $quote->getPayment();
        $store = Mage::app()->getStore($quote->getStoreId());

        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return $this;
        }

        if (!$quote->getCustomerId()) {
            return $this;
        }

        if (isset($request['payment']) && isset($request['payment']['use_customer_balance'])) {
            $use = $request['payment']['use_customer_balance'];

            $quote->setUseCustomerBalance($request['payment']['use_customer_balance']);
            if ($use) {
                $balance = Mage::getModel('enterprise_customerbalance/balance')
                    ->setCustomerId($quote->getCustomerId())
                    ->setWebsiteId($store->getWebsiteId())
                    ->loadByCustomer()
                    ->getAmount();

                if ($balance) {
                    $total = $quote->getBaseGrandTotal()+$quote->getBaseCustomerBalanceAmountUsed();
                    if ($balance >= $total) {
                        $payment->setMethod('free');
                    }
                } else {
                    $quote->setUseCustomerBalance(false);
                }
            }
        }

        return $this;
    }

    /**
     * Set the flag that we need to collect overall totals
     *
     * @param Varien_Event_Observer $observer
     */
    public function quoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setCustomerBalanceCollected(false);
    }

    /**
     * Set the source customer balance usage flag into new quote
     *
     * @param Varien_Event_Observer $observer
     */
    public function quoteMergeAfter(Varien_Event_Observer $observer)
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CustomerBalance_Model_Observer
     */
    public function increaseOrderInvoicedAmount(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        if ($invoice->getBaseCustomerBalanceAmount()) {
            $order->setBaseCustomerBalanceInvoiced($order->getBaseCustomerBalanceInvoiced() + $invoice->getBaseCustomerBalanceAmount());
            $order->setCustomerBalanceInvoiced($order->getCustomerBalanceInvoiced() + $invoice->getCustomerBalanceAmount());
        }

        return $this;
    }


    /**
     * Refund process
     * used for event: sales_order_creditmemo_save_after
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CustomerBalance_Model_Observer
     */
    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseCustomerBalanceTotalRefunded()) {
            $order->setBaseCustomerBalanceTotalRefunded($order->getBaseCustomerBalanceTotalRefunded() + $creditmemo->getBaseCustomerBalanceTotalRefunded());
            $order->setCustomerBalanceTotalRefunded($order->getCustomerBalanceTotalRefunded() + $creditmemo->getCustomerBalanceTotalRefunded());

            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();

            $balance = Mage::getModel('enterprise_customerbalance/balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->setAmountDelta($creditmemo->getBaseCustomerBalanceTotalRefunded())
                ->setHistoryAction(Enterprise_CustomerBalance_Model_Balance_History::ACTION_REFUNDED)
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CustomerBalance_Model_Observer
     */
    public function creditmemoDataImport(Varien_Event_Observer $observer)
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
                    $creditmemo->setBaseCustomerBalanceTotalRefunded($amount);

                    $amount = $creditmemo->getStore()->roundPrice(
                        $amount*$creditmemo->getOrder()->getStoreToOrderRate()
                    );
                    $creditmemo->setCustomerBalanceTotalRefunded($amount);

                    $creditmemo->setPaymentRefundDisallowed(true);
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CustomerBalance_Model_Observer
     */
    public function salesOrderLoadAfter(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->getState() === Mage_Sales_Model_Order::STATE_CANCELED ||
            $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CustomerBalance_Model_Observer
     */
    public function refund(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();


        if ($creditmemo->getRefundRealCustomerBalance() && $creditmemo->getBaseGrandTotal()) {
            $baseAmount = $creditmemo->getBaseGrandTotal();
            $amount = $creditmemo->getGrandTotal();

            $creditmemo->setBaseCustomerBalanceTotalRefunded($creditmemo->getBaseCustomerBalanceTotalRefunded() + $baseAmount);
            $creditmemo->setCustomerBalanceTotalRefunded($creditmemo->getCustomerBalanceTotalRefunded() + $amount);
        }

        if ($creditmemo->getBaseCustomerBalanceAmount()) {
            if ($creditmemo->getRefundCustomerBalance()) {
                $baseAmount = $creditmemo->getBaseCustomerBalanceAmount();
                $amount = $creditmemo->getCustomerBalanceAmount();

                $creditmemo->setBaseCustomerBalanceTotalRefunded($creditmemo->getBaseCustomerBalanceTotalRefunded() + $baseAmount);
                $creditmemo->setCustomerBalanceTotalRefunded($creditmemo->getCustomerBalanceTotalRefunded() + $amount);
            }

            $order->setBaseCustomerBalanceRefunded($order->getBaseCustomerBalanceRefunded() + $creditmemo->getBaseCustomerBalanceAmount());
            $order->setCustomerBalanceRefunded($order->getCustomerBalanceRefunded() + $creditmemo->getCustomerBalanceAmount());
        }

        return $this;
    }

    /**
     * Defined in Logging/etc/logging.xml - special handler for setting second action for customerBalance change
     *
     * @param string action
     */
    public function predispatchPrepareLogging($action) {
        $request = Mage::app()->getRequest();
        $data = $request->getParam('customerbalance');
        if (isset($data['amount_delta']) && $data['amount_delta'] != '') {
            $actions = Mage::registry('enterprise_logged_actions');
            if (!is_array($actions)) {
                $actions = array($actions);
            }
            $actions[] = 'adminhtml_customerbalance_save';
            Mage::unregister('enterprise_logged_actions');
            Mage::register('enterprise_logged_actions', $actions);
        }
    }

    /**
     * Set customers balance currency code to website base currency code on website deletion
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CustomerBalance_Model_Observer
     */
    public function setCustomersBalanceCurrencyToWebsiteBaseCurrency(Varien_Event_Observer $observer)
    {
        Mage::getModel('enterprise_customerbalance/balance')->setCustomersBalanceCurrencyTo(
            $observer->getEvent()->getWebsite()->getBaseCurrencyCode()
        );
        return $this;
    }
}
