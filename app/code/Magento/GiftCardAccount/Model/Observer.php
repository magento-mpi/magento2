<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Model_Observer
{
    /**
     * Charge all gift cards applied to the order
     * used for event: sales_order_place_after
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function processOrderPlace(Magento_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $cards = Mage::helper('Magento_GiftCardAccount_Helper_Data')->getCards($order);
        if (is_array($cards)) {
            foreach ($cards as &$card) {
                $args = array(
                    'amount'=>$card['ba'],
                    'giftcardaccount_id'=>$card['i'],
                    'order'=>$order
                );

                Mage::dispatchEvent('magento_giftcardaccount_charge', $args);
                $card['authorized'] = $card['ba'];
            }

            $cards = Mage::helper('Magento_GiftCardAccount_Helper_Data')->setCards($order, $cards);
        }

        return $this;
    }

    /**
     * Process order place before
     *
     * @param Magento_Event_Observer $observer
     * @return
     */
    public function processOrderCreateBefore(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $cards = Mage::helper('Magento_GiftCardAccount_Helper_Data')->getCards($quote);

        if (is_array($cards)) {
            foreach ($cards as $card) {
                /** @var $giftCardAccount Magento_GiftCardAccount_Model_Giftcardaccount */
                $giftCardAccount = Mage::getSingleton('Magento_GiftCardAccount_Model_Giftcardaccount');
                $giftCardAccount->load($card['i']);
                try {
                    $giftCardAccount->isValid(true, true, false, (float)$quote->getBaseGiftCardsAmountUsed());
                } catch (Magento_Core_Exception $e) {
                    $quote->setErrorMessage($e->getMessage());
                }
            }
        }
    }

    /**
     * Charge specified Gift Card (using code)
     * used for event: magento_giftcardaccount_charge_by_code
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function chargeByCode(Magento_Event_Observer $observer)
    {
        $id = $observer->getEvent()->getGiftcardaccountCode();
        $amount = $observer->getEvent()->getAmount();

        Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
            ->loadByCode($id)
            ->charge($amount)
            ->setOrder($observer->getEvent()->getOrder())
            ->save();

        return $this;
    }

    /**
     * Charge specified Gift Card (using id)
     * used for event: magento_giftcardaccount_charge
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function chargeById(Magento_Event_Observer $observer)
    {
        $id = $observer->getEvent()->getGiftcardaccountId();
        $amount = $observer->getEvent()->getAmount();

        Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
            ->load($id)
            ->charge($amount)
            ->setOrder($observer->getEvent()->getOrder())
            ->save();

        return $this;
    }

    /**
     * Increase order giftcards_amount_invoiced attribute based on created invoice
     * used for event: sales_order_invoice_register
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function increaseOrderGiftCardInvoicedAmount(Magento_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getBaseGiftCardsAmount()) {
            $order->setBaseGiftCardsInvoiced($order->getBaseGiftCardsInvoiced() + $invoice->getBaseGiftCardsAmount());
            $order->setGiftCardsInvoiced($order->getGiftCardsInvoiced() + $invoice->getGiftCardsAmount());
        }
        return $this;
    }

    /**
     * Create gift card account on event
     * used for event: magento_giftcardaccount_create
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function create(Magento_Event_Observer $observer)
    {
        $data = $observer->getEvent()->getRequest();
        $code = $observer->getEvent()->getCode();
        $order = $data->getOrder() ?: ($data->getOrderItem()->getOrder() ?: null);

        $model = Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
            ->setStatus(Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED)
            ->setWebsiteId($data->getWebsiteId())
            ->setBalance($data->getAmount())
            ->setLifetime($data->getLifetime())
            ->setIsRedeemable($data->getIsRedeemable())
            ->setOrder($order)
            ->save();

        $code->setCode($model->getCode());

        return $this;
    }

    /**
     * Save history on gift card account model save event
     * used for event: magento_giftcardaccount_save_after
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function giftcardaccountSaveAfter(Magento_Event_Observer $observer)
    {
        $gca = $observer->getEvent()->getGiftcardaccount();

        if ($gca->hasHistoryAction()) {
            Mage::getModel('Magento_GiftCardAccount_Model_History')
                ->setGiftcardaccount($gca)
                ->save();
        }

        return $this;
    }


    /**
     * Process post data and set usage of GC into order creation model
     *
     * @param Magento_Event_Observer $observer
     */
    public function processOrderCreationData(Magento_Event_Observer $observer)
    {
        $model = $observer->getEvent()->getOrderCreateModel();
        $request = $observer->getEvent()->getRequest();
        $quote = $model->getQuote();
        if (isset($request['giftcard_add'])) {
            $code = $request['giftcard_add'];
            try {
                Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart(true, $quote);
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->addException(
                    $e,
                    __('We cannot apply this gift card.')
                );
            }
        }

        if (isset($request['giftcard_remove'])) {
            $code = $request['giftcard_remove'];

            try {
                Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart(false, $quote);
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->addException(
                    $e,
                    __('We cannot remove this gift card.')
                );
            }
        }
        return $this;
    }

    /**
     * Set flag that giftcard applied on payment step in checkout process
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function paymentDataImport(Magento_Event_Observer $observer)
    {
        /* @var $quote Magento_Sales_Model_Quote */
        $quote = $observer->getEvent()->getPayment()->getQuote();
        if (!$quote || !$quote->getCustomerId()) {
            return $this;
        }
        /* Gift cards validation */
        $cards = Mage::helper('Magento_GiftCardAccount_Helper_Data')->getCards($quote);
        $website = Mage::app()->getStore($quote->getStoreId())->getWebsite();
        foreach ($cards as $one) {
            Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
                ->loadByCode($one['c'])
                ->isValid(true, true, $website);
        }

        if ((float)$quote->getBaseGiftCardsAmountUsed()) {
            $quote->setGiftCardAccountApplied(true);
            $input = $observer->getEvent()->getInput();
            if (!$input->getMethod()) {
                $input->setMethod('free');
            }
        }
        return $this;
    }

    /**
     * Force Zero Subtotal Checkout if the grand total is completely covered by SC and/or GC
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function togglePaymentMethods($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }
        // check if giftcard applied and then try to use free method
        if (!$quote->getGiftCardAccountApplied()) {
            return;
        }
        // disable all payment methods and enable only Zero Subtotal Checkout and Google Checkout
        if ($quote->getBaseGrandTotal() == 0 && (float)$quote->getGiftCardsAmountUsed()) {
            $paymentMethod = $observer->getEvent()->getMethodInstance()->getCode();
            $result = $observer->getEvent()->getResult();
            // allow customer to place order via google checkout even if grand total is zero
            $result->isAvailable = ($paymentMethod === 'free' || $paymentMethod === 'googlecheckout')
                && empty($result->isDeniedInConfig);
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
        $quote->setGiftCardsTotalCollected(false);
    }


    /**
     * Set the source gift card accounts into new quote
     *
     * @param Magento_Event_Observer $observer
     */
    public function quoteMergeAfter(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $source = $observer->getEvent()->getSource();

        if ($source->getGiftCards()) {
            $quote->setGiftCards($source->getGiftCards());
        }
    }

    /**
     * Set refund amount to creditmemo
     * used for event: sales_order_creditmemo_refund
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function refund(Magento_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseGiftCardsAmount()) {
            if ($creditmemo->getRefundGiftCards()){
                $baseAmount = $creditmemo->getBaseGiftCardsAmount();
                $amount = $creditmemo->getGiftCardsAmount();

                $creditmemo->setBsCustomerBalTotalRefunded($creditmemo->getBsCustomerBalTotalRefunded() + $baseAmount);
                $creditmemo->setCustomerBalTotalRefunded($creditmemo->getCustomerBalTotalRefunded() + $amount);
            }

            $order->setBaseGiftCardsRefunded(
                $order->getBaseGiftCardsRefunded() + $creditmemo->getBaseGiftCardsAmount()
            );
            $order->setGiftCardsRefunded($order->getGiftCardsRefunded() + $creditmemo->getGiftCardsAmount());

            // we need to update flag after credit memo was refunded and order's properties changed
            if ($order->getGiftCardsInvoiced() > 0 &&
                $order->getGiftCardsInvoiced() == $order->getGiftCardsRefunded()
            ) {
                $order->setForcedCanCreditmemo(false);
            }
        }

        return $this;
    }

    /**
     * Set refund flag to creditmemo based on user input
     * used for event: adminhtml_sales_order_creditmemo_register_before
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function creditmemoDataImport(Magento_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $input = $request->getParam('creditmemo');

        if (isset($input['refund_giftcardaccount']) && $input['refund_giftcardaccount']) {
            $creditmemo->setRefundGiftCards(true);
        }

        return $this;
    }

    /**
     * Set forced canCreditmemo flag
     * used for event: sales_order_load_after
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
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

        if ($order->getGiftCardsInvoiced() - $order->getGiftCardsRefunded() >= 0.0001) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }

    /**
     * Updating price for Google Checkout internal discount item.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCardAccount_Model_Observer
     */
    public function googleCheckoutDiscoutItem(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $discountItem = $observer->getEvent()->getDiscountItem();
        // discount price is negative value
        $discountItem->setPrice($discountItem->getPrice() - $quote->getBaseGiftCardsAmountUsed());

        return $this;
    }

    /**
     * Merge gift card amount into discount of PayPal checkout totals
     *
     * @param Magento_Event_Observer $observer
     */
    public function addPaypalGiftCardItem(Magento_Event_Observer $observer)
    {
        $paypalCart = $observer->getEvent()->getPaypalCart();
        if ($paypalCart) {
            $salesEntity = $paypalCart->getSalesEntity();
            $value = abs($salesEntity->getBaseGiftCardsAmount());
            if ($value > 0.0001) {
                $paypalCart->updateTotal(Magento_Paypal_Model_Cart::TOTAL_DISCOUNT, $value,
                    __('Gift Card (%1)', Mage::app()->getStore()->convertPrice($value, true, false))
                );
            }
        }
    }

    /**
     * Revert amount to gift card
     *
     * @param   int $id
     * @param   float $amount
     * @return  Magento_GiftCardAccount_Model_Observer
     */
    protected function _revertById($id, $amount = 0)
    {
        $giftCard = Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')->load($id);

        if ($giftCard) {
            $giftCard->revert($amount)
                ->unsOrder()
                ->save();
        }

        return $this;
    }

    /**
     * Revert authorized amounts for all order's gift cards
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_GiftCardAccount_Model_Observer
     */
    protected function _revertGiftCardsForOrder(Magento_Sales_Model_Order $order)
    {
        $cards = Mage::helper('Magento_GiftCardAccount_Helper_Data')->getCards($order);
        if (is_array($cards)) {
            foreach ($cards as $card) {
                if (isset($card['authorized'])) {
                    $this->_revertById($card['i'], $card['authorized']);
                }
            }
        }

        return $this;
    }

    /**
     * Revert authorized amounts for all order's gift cards
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_GiftCardAccount_Model_Observer
     */
    public function revertGiftCardAccountBalance(Magento_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order) {
            $this->_revertGiftCardsForOrder($order);
        }

        return $this;
    }

    /**
     * Revert gift cards for all orders
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_GiftCardAccount_Model_Observer
     */
    public function revertGiftCardsForAllOrders(Magento_Event_Observer $observer)
    {
        $orders = $observer->getEvent()->getOrders();

        foreach ($orders as $order) {
            $this->_revertGiftCardsForOrder($order);
        }

        return $this;
    }

    /**
     * Return funds to store credit
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_GiftCardAccount_Model_Observer
     */
    public function returnFundsToStoreCredit(Magento_Event_Observer $observer)
    {
        /** @var Magento_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        $cards = Mage::helper('Magento_GiftCardAccount_Helper_Data')->getCards($order);
        if (is_array($cards)) {
            $balance = 0;
            foreach ($cards as $card) {
                $balance += $card['ba'];
            }

            if ($balance > 0) {
                Mage::getModel('Magento_CustomerBalance_Model_Balance')
                    ->setCustomerId($order->getCustomerId())
                    ->setWebsiteId(Mage::app()->getStore($order->getStoreId())->getWebsiteId())
                    ->setAmountDelta($balance)
                    ->setHistoryAction(Magento_CustomerBalance_Model_Balance_History::ACTION_REVERTED)
                    ->setOrder($order)
                    ->save();
            }
        }

        return $this;
    }

    /**
     * Extend sales amount expression with gift card refunded value
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function extendSalesAmountExpression(Magento_Event_Observer $observer)
    {
        /** @var $expressionTransferObject Magento_Object */
        $expressionTransferObject = $observer->getEvent()->getExpressionObject();
        /** @var $adapter Magento_DB_Adapter_Interface */
        $adapter = $observer->getEvent()->getCollection()->getConnection();
        $expressionTransferObject->setExpression($expressionTransferObject->getExpression() . ' - (%s)');
        $arguments = $expressionTransferObject->getArguments();
        $arguments[] = $adapter->getCheckSql(
            $adapter->prepareSqlCondition('main_table.base_gift_cards_refunded', array('null' => null)),
            0,
            sprintf(
                'main_table.base_gift_cards_refunded - %s - %s',
                $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                $adapter->getIfNullSql('main_table.base_shipping_refunded', 0)
            )
        );
        $expressionTransferObject->setArguments($arguments);
    }
}
