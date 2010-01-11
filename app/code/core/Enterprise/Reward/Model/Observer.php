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
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward observer
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Observer
{
    /**
     * Update reward points for customer, send notification
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function saveRewardPoints($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return;
        }

        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();
        if ($data = $request->getPost('reward')) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomer($customer)
                ->loadByCustomer();
            if (!empty($data['points_delta'])) {
                $reward->setData($data)
                    ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN)
                    ->setActionEntity($customer)
                    ->setRewardUpdateNotification((isset($data['reward_update_notification']) ? true : false))
                    ->setRewardWarningNotification((isset($data['reward_warning_notification']) ? true : false))
                    ->updateRewardPoints();
            } else {
                $reward->setRewardUpdateNotification((isset($data['reward_update_notification']) ? true : false))
                    ->setRewardWarningNotification((isset($data['reward_warning_notification']) ? true : false))
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Update reward points after customer register
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function customerRegister($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        if ($customer->isObjectNew()) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomer($customer)
                ->setActionEntity($customer)
                ->setStore(Mage::app()->getStore()->getId())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_REGISTER)
                ->updateRewardPoints();
        }
        return $this;
    }

    /**
     * Update points balance after review submit
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function reviewSubmit($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $review Mage_Review_Model_Review */
        $review = $observer->getEvent()->getObject();
        if ($review->isApproved() && $review->getCustomerId()) {
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($review->getCustomerId())
                ->setStore($review->getStoreId())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_REVIEW)
                ->setActionEntity($review)
                ->updateRewardPoints();
        }
        return $this;
    }

    /**
     * Update points balance after tag submit
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function tagSubmit($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $tag Mage_Tag_Model_Tag */
        $tag = $observer->getEvent()->getObject();
        if (($tag->getApprovedStatus() == $tag->getStatus()) && $tag->getFirstCustomerId()) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($tag->getFirstCustomerId())
                ->setStore($tag->getFirstStoreId())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_TAG)
                ->setActionEntity($tag)
                ->updateRewardPoints();
        }
        return $this;
    }

    /**
     * Update points balance after first successful subscribtion
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function customerSubscribed($observer)
    {
        /* @var $subscriber Mage_Newsletter_Model_Subscriber */
        $subscriber = $observer->getEvent()->getSubscriber();
        // reward only new subscribtions
        if (!$subscriber->isObjectNew() || !$subscriber->getCustomerId()) {
            return $this;
        }

        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }

        $reward = Mage::getModel('enterprise_reward/reward')
            ->setCustomerId($subscriber->getCustomerId())
            ->setStore($subscriber->getStoreId())
            ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_NEWSLETTER)
            ->setActionEntity($subscriber)
            ->updateRewardPoints();

        return $this;
    }

    /**
     * Update points balance after customer registered by invitation
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function invitationToCustomer($observer)
    {
        /* @var $invitation Enterprise_Invitation_Model_Invitation */
        $invitation = $observer->getEvent()->getInvitation();

        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }

        if ($invitation->getCustomerId() && $invitation->getReferralId()) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($invitation->getCustomerId())
                ->setWebsiteId(Mage::app()->getStore($invitation->getStoreId())->getWebsiteId())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_CUSTOMER)
                ->setActionEntity($invitation)
                ->updateRewardPoints();
        }

        return $this;
    }

    /**
     * Update points balance after order becomes completed
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function orderCompleted($observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        /* @var $invitation Mage_Sales_Model_Order */
        if ($order->getCustomerIsGuest() || !Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        if ($order->getCustomerId() && ((float)$order->getBaseTotalInvoiced() > 0)
            && (($order->getBaseGrandTotal() - $order->getBaseSubtotalCanceled()) == $order->getBaseTotalPaid())) {
            $orderCollectedPoints = 0;
            $salesrulePointsDelta = 0;
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($order->getStore()->getWebsiteId())
                ->setActionEntity($order)
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER_EXTRA)
                ->updateRewardPoints();
            $orderCollectedPoints = $reward->getPointsDelta();
            if ($order->getRewardSalesrulePoints()) {
                $reward = Mage::getModel('enterprise_reward/reward')
                    ->setCustomerId($order->getCustomerId())
                    ->setWebsiteId($order->getStore()->getWebsiteId())
                    ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_SALESRULE)
                    ->setActionEntity($order)
                    ->setPointsDelta($order->getRewardSalesrulePoints())
                    ->updateRewardPoints();
                $salesrulePointsDelta = $reward->getPointsDelta();
            }
            if ($orderCollectedPoints) {
                $order->addStatusHistoryComment(
                    Mage::helper('enterprise_reward')->__('Collected %d Points to Customer', $orderCollectedPoints)
                )->save();
            }
            if ($salesrulePointsDelta) {
                $order->addStatusHistoryComment(
                    Mage::helper('enterprise_reward')->__('Gained Promotion %d Points to Customer', $salesrulePointsDelta)
                )->save();
            }
            // Also update inviter balance if possible
            $this->_invitationToOrder($observer);
        }

        return $this;
    }

    /**
     * Update inviter points balance after referral's order completed
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    protected function _invitationToOrder($observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        $invitation = Mage::getModel('enterprise_invitation/invitation')
            ->load($order->getCustomerId(), 'referral_id');
        if (!$invitation->getId() || !$invitation->getCustomerId()) {
            return $this;
        }
        $reward = Mage::getModel('enterprise_reward/reward')
            ->setActionEntity($order)
            ->setCustomerId($invitation->getCustomerId())
            ->setStore($order->getStoreId())
            ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER)
            ->updateRewardPoints();

        return $this;
    }

    /**
     * Set flag to reset reward points totals
     *
     * @param Varien_Event_Observer $observer
     * @@return Enterprise_Reward_Model_Observer
     */
    public function quoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setRewardPointsTotalReseted(false);
        return $this;
    }

    /**
     * Set use reward points flag to new quote
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function paymentDataImport(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        $input = $observer->getEvent()->getInput();
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getPayment()->getQuote();
        $this->_paymentDataImport($quote, $input, $input->getUseRewardPoints());
        return $this;
    }

    /**
     * Enable Zero Subtotal Checkout payment method
     * if customer has enough points to cover grand total
     *
     * @param Varien_Event_Observer $observer
     */
    public function preparePaymentMethod($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        $quote = $observer->getEvent()->getQuote();
        if (!$quote->getId()) {
            return $this;
        }
        /* @var $reward Enterprise_Reward_Model_Reward */
        $reward = $quote->getRewardInstance();
        if (!$reward || !$reward->getId()) {
            return $this;
        }
        $baseQuoteGrandTotal = $quote->getBaseGrandTotal()+$quote->getBaseRewardCurrencyAmount();
        if ($reward->isEnoughPointsToCoverAmount($baseQuoteGrandTotal)) {
            $paymentCode = $observer->getEvent()->getMethodInstance()->getCode();
            $result = $observer->getEvent()->getResult();
            if ('free' === $paymentCode) {
                $result->isAvailable = true;
            } else {
                $result->isAvailable = false;
            }
        }
        return $this;
    }

    /**
     * Payment data import in admin order create process
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function processOrderCreationData(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $quote Mage_Sales_Model_Quote */
        $request = $observer->getEvent()->getRequest();
        if (isset($request['payment']) && isset($request['payment']['use_reward_points'])) {
            $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
            $this->_paymentDataImport($quote, $quote->getPayment(), $request['payment']['use_reward_points']);
        }
        return $this;
    }

    /**
     * Prepare and set to quote reward balance instance,
     * set zero subtotal checkout payment if need
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Varien_Object $payment
     * @param boolean $useRewardPoints
     * @return Enterprise_Reward_Model_Observer
     */
    protected function _paymentDataImport($quote, $payment, $useRewardPoints)
    {
        if (!$quote || !$quote->getCustomerId()) {
            return $this;
        }
        $quote->setUseRewardPoints((bool)$useRewardPoints);
        if ($quote->getUseRewardPoints()) {
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomer($quote->getCustomer())
                ->setWebsiteId($quote->getStore()->getWebsiteId())
                ->loadByCustomer();
            if ($reward->getId()) {
                $quote->setRewardInstance($reward);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                }
            }
            else {
                $quote->setUseRewardPoints(false);
            }
        }
        return $this;
    }

    /**
     * Reduce reward points if points was used during checkout
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function processOrderPlace(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->getBaseRewardCurrencyAmount() > 0) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId(Mage::app()->getStore($order->getStoreId())->getWebsiteId())
                ->setPointsDelta(-$order->getRewardPointsBalance())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER)
                ->setActionEntity($order)
                ->updateRewardPoints();
        }
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);
        $data = Mage::getResourceModel('enterprise_reward/reward')
            ->getRewardSalesrule($ruleIds);
        $pointsDelta = 0;
        foreach ($data as $rule) {
            $pointsDelta += (int)$rule['points_delta'];
        }
        if ($pointsDelta) {
            $order->setRewardSalesrulePoints($pointsDelta);
        }
        return $this;
    }

    /**
     * Set forced can creditmemo flag if refunded amount less then invoiced amount of reward points
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function orderLoadAfter(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->canUnhold()) {
            return $this;
        }
        if ($order->isCanceled() ||
            $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
            return $this;
        }
        if (($order->getBaseRewardCurrencyAmountInvoiced() - $order->getBaseRewardCurrencyAmountRefunded()) > 0) {
            $order->setForcedCanCreditmemo(true);
        }
        return $this;
    }

    /**
     * Set invoiced reward amount to order
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function invoiceSaveAfter(Varien_Event_Observer $observer)
    {
        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getBaseRewardCurrencyAmount()) {
            $order = $invoice->getOrder();
            $order->setRewardCurrencyAmountInvoiced($order->getRewardCurrencyAmountInvoiced() + $invoice->getRewardCurrencyAmount());
            $order->setBaseRewardCurrencyAmountInvoiced($order->getBaseRewardCurrencyAmountInvoiced() + $invoice->getBaseRewardCurrencyAmount());
        }
        return $this;
    }

    /**
     * Set reward points balance to refund before creditmemo register
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function setRewardPointsBalanceToRefund(Varien_Event_Observer $observer)
    {
        $input = $observer->getEvent()->getRequest()->getParam('creditmemo');
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (isset($input['refund_reward_points']) && isset($input['refund_reward_points_enable'])) {
            $enable = $input['refund_reward_points_enable'];
            $balance = (int)$input['refund_reward_points'];
            $balance = min($creditmemo->getRewardPointsBalance(), $balance);
            if ($enable && $balance) {
                $creditmemo->setRewardPointsBalanceToRefund($balance);
            }
        }
        return $this;
    }

    /**
     * Clear forced can creditmemo if whole reward amount was refunded
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function creditmemoRefund(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getCreditmemo()->getOrder();
        $refundedAmount = (float)($order->getBaseRewardCurrencyAmountRefunded() + $creditmemo->getBaseRewardCurrencyAmount());
        if ((float)$order->getBaseRewardCurrencyAmountInvoiced() == $refundedAmount) {
            $order->setForcedCanCreditmemo(false);
        }
        return $this;
    }

    /**
     * Set refunded reward amount order and update reward points balance if need
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo->getBaseRewardCurrencyAmount()) {
            $order = $creditmemo->getOrder();
            $order->setRewardPointsBalanceRefunded($order->getRewardPointsBalanceRefunded() + $creditmemo->getRewardPointsBalance());
            $order->setRewardCurrencyAmountRefunded($order->getRewardCurrencyAmountRefunded() + $creditmemo->getRewardCurrencyAmount());
            $order->setBaseRewardCurrencyAmountRefunded($order->getBaseRewardCurrencyAmountRefunded() + $creditmemo->getBaseRewardCurrencyAmount());
            $order->setRewardPointsBalanceToRefund($order->getRewardPointsBalanceToRefund() + $creditmemo->getRewardPointsBalanceToRefund());

            if ((int)$creditmemo->getRewardPointsBalanceToRefund() > 0) {
                $reward = Mage::getModel('enterprise_reward/reward')
                    ->setCustomerId($order->getCustomerId())
                    ->setStore($order->getStoreId())
                    ->setPointsDelta((int)$creditmemo->getRewardPointsBalanceToRefund())
                    ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_CREDITMEMO)
                    ->setActionEntity($order)
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Disable entire RP layout
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function disableLayout(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            unset($observer->getUpdates()->enterprise_reward);
        }
        return $this;
    }

    /**
     * Send scheduled low balance warning notifications
     *
     * @return Enterprise_Reward_Model_Observer
     */
    public function scheduledBalanceExpireNotification()
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        foreach (Mage::app()->getWebsites() as $website) {
            $inDays = (int)Mage::helper('enterprise_reward')->getNotificationConfig('expiry_day_before');
            if (!$inDays) {
                continue;
            }
            $collection = Mage::getResourceModel('enterprise_reward/reward_history_collection')
                ->setExpiryConfig(Mage::helper('enterprise_reward')->getExpiryConfig())
                ->loadExpiredSoonPoints($website->getId(), true)
                ->addCustomerInfo()
                ->setPageSize(20) // limit queues for each website
                ->setCurPage(1)
                ->load();

            foreach ($collection as $item) {
                Mage::getSingleton('enterprise_reward/reward')->sendBalanceWarningNotification($item);
            }
        }

        return $this;
    }

    /**
     * Make points expired
     *
     * @return Enterprise_Reward_Model_Observer
     */
    public function scheduledPointsExpiration()
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        foreach (Mage::app()->getWebsites() as $website) {
            $expiryType = Mage::helper('enterprise_reward')->getGeneralConfig('expiry_calculation', $website->getId());
            Mage::getResourceModel('enterprise_reward/reward_history')
                ->expirePoints($website->getId(), $expiryType, 100);
        }

        return $this;
    }

    /**
     * Prepare orphan points of customers after website was deleted
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function prepareCustomerOrphanPoints(Varien_Event_Observer $observer)
    {
        /* @var $website Mage_Core_Model_Website */
        $website = $observer->getEvent()->getWebsite();
        Mage::getModel('enterprise_reward/reward')->prepareOrphanPoints($website->getId(), $website->getBaseCurrencyCode());
        return $this;
    }

    /**
     * Prepare salesrule form. Add field to specify reward points delta
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function prepareSalesruleForm(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('action_fieldset');
        $fieldset->addField('reward_points_delta', 'text', array(
            'name'  => 'reward_points_delta',
            'label' => Mage::helper('enterprise_reward')->__('Add Reward Points'),
            'title' => Mage::helper('enterprise_reward')->__('Add Reward Points')
        ), 'stop_rules_processing');
        return $this;
    }

    /**
     * Set reward points delta to salesrule model after it loaded
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function loadRewardSalesruleData(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $salesRule Mage_SalesRule_Model_Rule */
        $salesRule = $observer->getEvent()->getRule();
        if ($salesRule->getId()) {
            $data = Mage::getResourceModel('enterprise_reward/reward')
                ->getRewardSalesrule($salesRule->getId());
            if (isset($data['points_delta'])) {
                $salesRule->setRewardPointsDelta($data['points_delta']);
            }
        }
        return $this;
    }

    /**
     * Save reward points delta for salesrule
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function saveRewardSalesruleData(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $salesRule Mage_SalesRule_Model_Rule */
        $salesRule = $observer->getEvent()->getRule();
        Mage::getResourceModel('enterprise_reward/reward')
            ->saveRewardSalesrule($salesRule->getId(), (int)$salesRule->getRewardPointsDelta());
        return $this;
    }
}
