<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model;

class Observer
{
    /**
     * Gift card account data
     *
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $_giftCAHelper = null;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * Gift card account giftcardaccount
     *
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $_giftCAFactory = null;

    /**
     * Gift card account history
     *
     * @var \Magento\GiftCardAccount\Model\History
     */
    protected $_giftCAHistory = null;

    /**
     * Customer balance balance
     *
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_customerBalance = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Store Manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param \Magento\CustomerBalance\Model\Balance $customerBalance
     * @param \Magento\GiftCardAccount\Model\History $giftCAHistory
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Magento\CustomerBalance\Model\Balance $customerBalance,
        \Magento\GiftCardAccount\Model\History $giftCAHistory,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_eventManager = $eventManager;
        $this->_giftCAHelper = $giftCAHelper;
        $this->_customerBalance = $customerBalance;
        $this->_giftCAHistory = $giftCAHistory;
        $this->_giftCAFactory = $giftCAFactory;
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
    }

    /**
     * Charge all gift cards applied to the order
     * used for event: sales_order_place_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function processOrderPlace(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $cards = $this->_giftCAHelper->getCards($order);
        if (is_array($cards)) {
            foreach ($cards as &$card) {
                $this->_giftCAFactory->create()->load($card['i'])->charge($card['ba'])->setOrder($order)->save();
                $card['authorized'] = $card['ba'];
            }

            $this->_giftCAHelper->setCards($order, $cards);
        }

        return $this;
    }

    /**
     * Charge specified Gift Card (using code)
     * used for event: magento_giftcardaccount_charge_by_code
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function chargeByCode(\Magento\Framework\Event\Observer $observer)
    {
        $id = $observer->getEvent()->getGiftcardaccountCode();
        $amount = $observer->getEvent()->getAmount();

        $this->_giftCAFactory->create()->loadByCode(
            $id
        )->charge(
            $amount
        )->setOrder(
            $observer->getEvent()->getOrder()
        )->save();

        return $this;
    }

    /**
     * Increase order giftcards_amount_invoiced attribute based on created invoice
     * used for event: sales_order_invoice_register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function increaseOrderGiftCardInvoicedAmount(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function create(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getEvent()->getRequest();
        $code = $observer->getEvent()->getCode();
        $order = $data->getOrder() ?: ($data->getOrderItem()->getOrder() ?: null);

        $model = $this->_giftCAFactory->create()->setStatus(
            \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED
        )->setWebsiteId(
            $data->getWebsiteId()
        )->setBalance(
            $data->getAmount()
        )->setLifetime(
            $data->getLifetime()
        )->setIsRedeemable(
            $data->getIsRedeemable()
        )->setOrder(
            $order
        )->save();

        $code->setCode($model->getCode());

        return $this;
    }

    /**
     * Save history on gift card account model save event
     * used for event: magento_giftcardaccount_save_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function giftcardaccountSaveAfter(\Magento\Framework\Event\Observer $observer)
    {
        $gca = $observer->getEvent()->getGiftcardaccount();

        if ($gca->hasHistoryAction()) {
            $this->_giftCAHistory->setGiftcardaccount($gca)->save();
        }

        return $this;
    }

    /**
     * Process post data and set usage of GC into order creation model
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function processOrderCreationData(\Magento\Framework\Event\Observer $observer)
    {
        $model = $observer->getEvent()->getOrderCreateModel();
        $request = $observer->getEvent()->getRequest();
        $quote = $model->getQuote();
        if (isset($request['giftcard_add'])) {
            $code = $request['giftcard_add'];
            try {
                $this->_giftCAFactory->create()->loadByCode($code)->addToCart(true, $quote);
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot apply this gift card.'));
            }
        }

        if (isset($request['giftcard_remove'])) {
            $code = $request['giftcard_remove'];

            try {
                $this->_giftCAFactory->create()->loadByCode($code)->removeFromCart(false, $quote);
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot remove this gift card.'));
            }
        }
        return $this;
    }

    /**
     * Set flag that giftcard applied on payment step in checkout process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function paymentDataImport(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getPayment()->getQuote();
        if (!$quote || !$quote->getCustomerId()) {
            return $this;
        }
        /* Gift cards validation */
        $cards = $this->_giftCAHelper->getCards($quote);
        $website = $this->_storeManager->getStore($quote->getStoreId())->getWebsite();
        foreach ($cards as $one) {
            $this->_giftCAFactory->create()->loadByCode($one['c'])->isValid(true, true, $website);
        }

        if ((double)$quote->getBaseGiftCardsAmountUsed()) {
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
     * @param \Magento\Framework\Event\Observer $observer
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
        // disable all payment methods and enable only Zero Subtotal Checkout
        if ($quote->getBaseGrandTotal() == 0 && (double)$quote->getGiftCardsAmountUsed()) {
            $paymentMethod = $observer->getEvent()->getMethodInstance()->getCode();
            $result = $observer->getEvent()->getResult();
            // allow customer to place order if grand total is zero
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
        $quote->setGiftCardsTotalCollected(false);
    }

    /**
     * Set the source gift card accounts into new quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function quoteMergeAfter(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function refund(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseGiftCardsAmount()) {
            if ($creditmemo->getRefundGiftCards()) {
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
            if ($order->getGiftCardsInvoiced() > 0 && $order->getGiftCardsInvoiced() == $order->getGiftCardsRefunded()
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function creditmemoDataImport(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $input = $observer->getEvent()->getInput();

        if (isset($input['refund_giftcardaccount']) && $input['refund_giftcardaccount']) {
            $creditmemo->setRefundGiftCards(true);
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

        if ($order->getGiftCardsInvoiced() - $order->getGiftCardsRefunded() >= 0.0001) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }

    /**
     * Merge gift card amount into discount of payment checkout totals
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addPaymentGiftCardItem(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $salesEntity = $cart->getSalesModel();
        $value = abs($salesEntity->getDataUsingMethod('base_gift_cards_amount'));
        if ($value > 0.0001) {
            $cart->addDiscount((double)$value);
        }
    }

    /**
     * Revert amount to gift card
     *
     * @param int $id
     * @param float $amount
     * @return $this
     */
    protected function _revertById($id, $amount = 0)
    {
        /** @var \Magento\GiftCardAccount\Model\Giftcardaccount $giftCard */
        $giftCard = $this->_giftCAFactory->create()->load($id);

        if ($giftCard) {
            $giftCard->revert($amount)->unsOrder()->save();
        }

        return $this;
    }

    /**
     * Revert authorized amounts for all order's gift cards
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    protected function _revertGiftCardsForOrder(\Magento\Sales\Model\Order $order)
    {
        $cards = $this->_giftCAHelper->getCards($order);
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function revertGiftCardAccountBalance(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function revertGiftCardsForAllOrders(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function returnFundsToStoreCredit(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $cards = $this->_giftCAHelper->getCards($order);
        if (is_array($cards)) {
            $balance = 0;
            foreach ($cards as $card) {
                $balance += $card['ba'];
            }

            if ($balance > 0) {
                $this->_customerBalance->setCustomerId(
                    $order->getCustomerId()
                )->setWebsiteId(
                    $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
                )->setAmountDelta(
                    $balance
                )->setHistoryAction(
                    \Magento\CustomerBalance\Model\Balance\History::ACTION_REVERTED
                )->setOrder(
                    $order
                )->save();
            }
        }

        return $this;
    }

    /**
     * Extend sales amount expression with gift card refunded value
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
