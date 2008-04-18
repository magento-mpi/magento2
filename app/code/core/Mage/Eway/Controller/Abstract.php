<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eway
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * eWAY Checkout Abstract Controller
 *
 * @category   Mage
 * @package    Mage_Eway
 * @author     Dmytro Vasylenko <dimav@varien.com>
 */
abstract class Mage_Eway_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton of Payment Model
     * need to be redeclared
     */
    abstract protected function _getModel();

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * when customer select eWay payment method
     */
    public function redirectAction()
    {
        $session = $this->getCheckout();

        $this->_getModel()->setCheckout($session);
        $this->_getModel()->setQuote($session->getQuote());

        $session->setEwayQuoteId($session->getQuoteId());
        $session->setEwayRealOrderId($session->getLastRealOrderId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatus(), Mage::helper('eway')->__('Customer was redirected to eWAY.'));
        $order->save();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock($this->_getModel()->getRedirectBlockType())->toHtml()
        );

        $session->unsQuoteId();
    }

    /**
     * eWay returns POST variables to this action
     */
    public function  successAction()
    {
        $status = $this->_checkReturnedPost();

        $session = $this->getCheckout();

        $session->unsEwayRealOrderId();
        $session->setQuoteId($session->getEwayQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();

        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        if($order->getId()) {
            $order->sendNewOrderEmail();
        }

        if ($status) {
            $this->_redirect('checkout/onepage/success');
        } else {
            $this->_redirect('eway/' . $this->_getModel()->getPaymentMethodType() . '/failure');
        }
    }

    /**
     * Display failure page if error
     *
     */
    public function failureAction()
    {
        if (!$this->getCheckout()->getEwayErrorMessage()) {
            $this->norouteAction();
            return;
        }

        $this->getCheckout()->clear();

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Checking POST variables.
     * Creating invoice if payment was successfull or cancel order if payment was declined
     */
    protected function _checkReturnedPost()
    {
        if (!$this->getRequest()->isPost()) {
            $this->norouteAction();
            return;
        }
        $status = true;
        $response = $this->getRequest()->getPost();

        if ($this->getCheckout()->getEwayRealOrderId() != $response['ewayTrxnNumber']) {
            $this->norouteAction();
            return;
        }

        $this->_getModel()->setResponse($response);

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response['ewayTrxnNumber']);

        if ($this->_getModel()->parseResponse()) {

            if ($order->canInvoice()) {
                $convertor = Mage::getModel('sales/convert_order');
                $invoice = $convertor->toInvoice($order);
                foreach ($order->getAllItems() as $orderItem) {
                    if (!$orderItem->getQtyToInvoice()) {
                        continue;
                    }
                    $item = $convertor->itemToInvoiceItem($orderItem);
                    $item->setQty($orderItem->getQtyToInvoice());
                    $invoice->addItem($item);
                }
                $invoice->collectTotals();
                $invoice->register()->capture();
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();

                $this->_getModel()->setTransactionId($response['ewayTrxnReference']);
                $order->addStatusToHistory($order->getStatus(), Mage::helper('eway')->__('Customer successfully returned from eWAY'));
            }
        } else {
            $this->_getModel()->setTransactionId($response['ewayTrxnReference']);
            $order->cancel();
            $order->addStatusToHistory($order->getStatus(), Mage::helper('eway')->__('Customer was rejected by eWAY'));
            $status = false;
            $this->getCheckout()->setEwayErrorMessage($response['eWAYresponseText']);
        }

        $order->save();

        return $status;
    }

}