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
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftCardAccount_Model_Observer
{
    /**
     * Charge all gift cards applied to the order
     * used for event: sales_order_place_after
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function processOrderPlace(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $cards = Mage::helper('enterprise_giftcardaccount')->getCards($order);
        if (is_array($cards)) {
            foreach ($cards as $card) {
                $args = array(
                    'amount'=>$card['ba'],
                    'giftcardaccount_id'=>$card['i'],
                    'order'=>$order,
                );

                Mage::dispatchEvent('enterprise_giftcardaccount_charge', $args);
            }
        }
        return $this;
    }

    /**
     * Charge specified Gift Card (using code)
     * used for event: enterprise_giftcardaccount_charge_by_code
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function chargeByCode(Varien_Event_Observer $observer)
    {
        $id = $observer->getEvent()->getGiftcardaccountCode();
        $amount = $observer->getEvent()->getAmount();

        Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
            ->loadByCode($id)
            ->charge($amount)
            ->setOrder($observer->getEvent()->getOrder())
            ->save();

        return $this;
    }

    /**
     * Charge specified Gift Card (using id)
     * used for event: enterprise_giftcardaccount_charge
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function chargeById(Varien_Event_Observer $observer)
    {
        $id = $observer->getEvent()->getGiftcardaccountId();
        $amount = $observer->getEvent()->getAmount();

        Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
            ->load($id)
            ->charge($amount)
            ->setOrder($observer->getEvent()->getOrder())
            ->save();

        return $this;
    }

    /**
     * Increase order giftcards_amount_invoiced attribute based on created invoice
     * used for event: sales_order_invoice_save_after
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function increaseOrderInvoicedAmount(Varien_Event_Observer $observer)
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
     * used for event: enterprise_giftcardaccount_create
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function create(Varien_Event_Observer $observer)
    {
        $data = $observer->getEvent()->getRequest();
        $code = $observer->getEvent()->getCode();
        if ($data->getOrder()) {
            $order = $data->getOrder();
        } elseif ($data->getOrderItem()->getOrder()) {
            $order = $data->getOrderItem()->getOrder();
        } else {
            $order = null;
        }

        $model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
            ->setStatus(Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED)
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
     * used for event: enterprise_giftcardaccount_save_after
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function giftcardaccountSaveAfter(Varien_Event_Observer $observer)
    {
        $gca = $observer->getEvent()->getGiftcardaccount();

        if ($gca->hasHistoryAction()) {
            Mage::getModel('enterprise_giftcardaccount/history')
                ->setGiftcardaccount($gca)
                ->save();
        }

        return $this;
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
        if (isset($request['giftcard_add'])) {
            $code = $request['giftcard_add'];
            try {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart(true, $quote);
                /*
                Mage::getSingleton('adminhtml/session_quote')->addSuccess(
                    $this->__('Gift Card "%s" was added successfully.', Mage::helper('core')->htmlEscape($code))
                );
                */
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session_quote')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session_quote')->addException(
                    $e,
                    $this->__('Cannot apply Gift Card, please try again later.')
                );
            }
        }

        if (isset($request['giftcard_remove'])) {
            $code = $request['giftcard_remove'];

            try {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart(false, $quote);
                /*
                Mage::getSingleton('adminhtml/session_quote')->addSuccess(
                    $this->__('Gift Card "%s" was removed successfully.', Mage::helper('core')->htmlEscape($code))
                );
                */
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session_quote')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session_quote')->addException(
                    $e,
                    $this->__('Cannot remove Gift Card, please try again later.')
                );
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
        $quote->setGiftCardsTotalCollected(false);
    }


    /**
     * Set the source gift card accounts into new quote
     *
     * @param Varien_Event_Observer $observer
     */
    public function quoteMergeAfter(Varien_Event_Observer $observer)
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function refund(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseGiftCardsAmount()) {
            if ($creditmemo->getRefundGiftCards()){
                $baseAmount = $creditmemo->getBaseGiftCardsAmount();
                $amount = $creditmemo->getGiftCardsAmount();

                $creditmemo->setBaseCustomerBalanceTotalRefunded($creditmemo->getBaseCustomerBalanceTotalRefunded() + $baseAmount);
                $creditmemo->setCustomerBalanceTotalRefunded($creditmemo->getCustomerBalanceTotalRefunded() + $amount);
            }

            $order->setBaseGiftCardsRefunded($order->getBaseGiftCardsRefunded() + $creditmemo->getBaseGiftCardsAmount());
            $order->setGiftCardsRefunded($order->getGiftCardsRefunded() + $creditmemo->getGiftCardsAmount());

            // we need to update flag after credit memo was refunded and order's properties changed
            if ($order->getGiftCardsInvoiced() == $order->getGiftCardsRefunded()) {
                $order->setForcedCanCreditmemo(false);
            }
        }

        return $this;
    }

    /**
     * Set refund flag to creditmemo based on user input
     * used for event: adminhtml_sales_order_creditmemo_register_before
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function creditmemoDataImport(Varien_Event_Observer $observer)
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
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

        if ($order->getGiftCardsInvoiced() - $order->getGiftCardsRefunded() > 0) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }
}
