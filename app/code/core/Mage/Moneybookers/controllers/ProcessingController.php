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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Moneybookers
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Moneybookers_ProcessingController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_CUSTOMER_ID = 'moneybookers/settings/customer_id';
    const XML_PATH_SECRET_KEY  = 'moneybookers/settings/secret_key';

    /**
     * Processing Block Type
     *
     * @var string
     */
    protected $_redirectBlockType      = 'moneybookers/processing';
    protected $_checkresponseBlockType = 'moneybookers/checkresponse';

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
     * when customer select Moneybookers payment method
     */
    public function redirectAction()
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock($this->_redirectBlockType)
                ->setOrder($order)
                ->toHtml()
        );
    }

    /**
     * when customer select Moneybookers payment method
     */
    public function paymentAction()
    {
        $session = $this->getCheckout();
        $session->setMoneybookersQuoteId($session->getQuoteId());
        $session->setMoneybookersRealOrderId($session->getLastRealOrderId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_HOLDED, Mage::helper('moneybookers')->__('Customer was redirected to Moneybookers.'));
        $order->save();

        $this->loadLayout();
        $this->renderLayout();

        $session->unsQuoteId();
    }

    /**
     * Moneybookers returns GET variables to this action
     */
    public function checkresponseAction()
    {
        if ($this->_checkReturnedGet()) {
            $session = $this->getCheckout();

            $session->unsMoneybookersRealOrderId();
            $session->setQuoteId($session->getMoneybookersQuoteId(true));
            $session->getQuote()->setIsActive(false)->save();

            if($this->_order->getId()) {
                $this->_order->sendNewOrderEmail();
            }

            $this->getResponse()->setBody(
                $this->getLayout()
                    ->createBlock($this->_checkresponseBlockType)
                    ->toHtml()
            );
        } else {
            $this->norouteAction();
        }
    }

    /**
     * Display failure page if error
     */
    public function failureAction()
    {
        if (!$this->getCheckout()->getMoneybookersErrorMessage()) {
            $this->norouteAction();
            return;
        }

        $this->getCheckout()->clear();

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Moneybookers returns to this page if payment is canceled
     */
    public function statusAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_printResponse('Error: No parameters specified');
            return;
        }

        $response = $this->getRequest()->getParams();

        if (!isset($response['transaction_id']) ||
            !isset($response['status']) ||
            !isset($response['merchant_id']) ||
            !isset($response['mb_amount']) ||
            !isset($response['mb_currency']) ||
            !isset($response['mb_transaction_id']) ||
            !isset($response['md5sig'])) {
            $this->_printResponse('Error: insufficient parameters submited');
            return;
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response['transaction_id']);
        if (!$order->getId()) {
            $this->_printResponse('Error: Wrong order id submited');
            return;
        }

        if (Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ID, $order->getStoreId()) != $response['merchant_id']) {
            $this->_printResponse('Error: Wrong merchant id submited');
            return;
        }

        $paymentInst = $order->getPayment()->getMethodInstance();
        $secret = Mage::getStoreConfig(self::XML_PATH_SECRET_KEY, $order->getStoreId());
        $md5_string = $response['merchant_id'] . $response['transaction_id'] . strtoupper(md5($secret)) . $response['mb_amount'] . $response['mb_currency'] . $response['status'];
        $result_of_md5 = strtoupper(md5($md5_string));

        if ($result_of_md5 != $response['md5sig']) {
            $this->_printResponse('Error: Wrong parameters submited');
            return;
        }

        $payment_well = false;
        switch($response['status']) {
            case '-2'://fail
                $order_status = Mage::helper('moneybookers')->__('Payment failed');
                break;
            case '-1'://cancel
                $order_status = Mage::helper('moneybookers')->__('Payment was canceled');
                break;
            case '2'://ok
                $order_status = Mage::helper('moneybookers')->__('The amount has been authorized and captured by Moneybookers.');
                $payment_well = true;
                break;
            case '0'://pending
            default:
                $order_status = Mage::helper('moneybookers')->__('Pending bank transfer created.');
                $payment_well = true;
                break;
        }

        $paymentInst->setTransactionId($response['mb_transaction_id']);
        if ($payment_well) {
            if ($order->canInvoice()) {
                $invoice = $order->prepareInvoice();

                $invoice->register()->capture();
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();
            }
            $order->addStatusToHistory($paymentInst->getConfigData('order_status'), $order_status);
        }
        else {
            $order->cancel();
            $order->addStatusToHistory($order->getState(), $order_status);

            if (isset($response['message']))
                $message = $response['message'];
            else
                $message = 'No error message was sent by Moneyboookers';
            $this->getCheckout()->setMoneybookersErrorMessage($message);
        }

        $order->save();

        $this->_printResponse($order_status);
    }

    /**
     * Checking POST variables.
     * Creating invoice if payment was successfull or cancel order if payment was declined
     */
    protected function _checkReturnedGet()
    {
        if (!$this->getRequest()->isGet()) {
            return false;
        }
        $response = $this->getRequest()->getParams();

        // check requiredparameters
        if (!isset($response['order_id']) || !isset($response['status'])) {
            return false;
        }

        // check order ID
        if ($this->getCheckout()->getMoneybookersRealOrderId() != $response['order_id']) {
            return false;
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response['order_id']);
        if(!$order->getId()) {
            return false;
        }
        $this->_order = $order;

        if ($response['status'] == 'success') {
            $this->getCheckout()->setMoneybookersRedirectUrl(Mage::getUrl('checkout/onepage/success'));
        }
        else if ($response['status'] == 'cancel') {
            $this->_order->cancel();
            $this->getCheckout()->setMoneybookersRedirectUrl(Mage::getUrl('*/*/failure'));
            $this->getCheckout()->setMoneybookersErrorMessage(Mage::helper('moneybookers')->__('Payment was canceled'));
        }
        else {
            return false;
        }

        $this->_order->save();

        return true;
    }

    /**
     * Printing simple HTMl response.
     */
    protected function _printResponse($message)
    {
        $html = '<html><body>';
        $html.= $message;
        $html.= '</body></html>';
        $this->getResponse()->setBody($html);
    }
}
