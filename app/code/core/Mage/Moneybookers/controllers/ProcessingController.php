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
 * @category    Mage
 * @package     Mage_Moneybookers
 * @copyright   Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Moneybookers_ProcessingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve Moneybookers helper
     *
     * @return Mage_Moneybookers_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('moneybookers');
    }

    /**
     * Iframe page which submits the payment data to Moneybookers.
     */
    public function placeformAction()
    {
       $this->loadLayout();
       $this->renderLayout();
    }

    /**
     * Show orderPlaceRedirect page which contains the Moneybookers iframe.
     */
    public function paymentAction()
    {
        try {
            $session = $this->_getCheckout();

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            $order->setState(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                $this->_getHelper()->__('Customer was redirected to Moneybookers.')
            );
            $order->save();

            $session->getQuote()->setIsActive(false)->save();
            $session->setMoneybookersQuoteId($session->getQuoteId());
            $session->setMoneybookersRealOrderId($session->getLastRealOrderId());
            $session->clear();

            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e){
            Mage::logException($e);
            parent::_redirect('checkout/cart');
        }
    }

    /**
     * Action to which the customer will be returned when the payment is made.
     */
    public function successAction()
    {
        try {
            $this->_checkReturnedData(false);

            $this->_getCheckout()->setLastSuccessQuoteId($this->_order->getQuoteId());

            $this->_redirect('checkout/onepage/success');

        } catch(Exception $e) {
            $this->_getCheckout()->addError($this->_getHelper()->__($e->getMessage()));
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Action to which the customer will be returned if the payment process is
     * cancelled.
     * Cancel order and redirect user to the shopping cart.
     */
    public function cancelAction()
    {
        try {
            $this->_checkReturnedData(false);

            // load quote
            $this->_getCheckout()->setQuoteId($this->_getCheckout()->getMoneybookersQuoteId());

            $this->_processCancel('Payment was canceled');

            $this->_getCheckout()->addError($this->_getHelper()->__('The order has been canceled.'));
            $this->_redirect('checkout/cart');

        } catch(Exception $e) {
            $this->_getCheckout()->addError($this->_getHelper()->__($e->getMessage()));
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Action to which the transaction details will be posted after the payment
     * process is complete.
     */
    public function statusAction()
    {
        try {
            $params = $this->_checkReturnedData();

            switch($params['status']) {
                case '-2': //fail
                    $msg = 'Payment failed';
                    $this->_processCancel($msg);
                    break;
                case '-1': //cancel
                    $msg = 'Payment was canceled';
                    $this->_processCancel($msg);
                    break;
                case '0': //pending
                    $msg = 'Pending bank transfer created.';
                    $this->_processSale($msg);
                    break;
                case '2': //ok
                    $msg = 'The amount has been authorized and captured by Moneybookers.';
                    $this->_processSale($msg);
                    break;
            }

            $this->getResponse()->setBody($this->_getHelper()->__($msg));
        } catch(Exception $e) {
            Mage::logException($e);
            $this->getResponse()->setBody('Error: ' . $this->_getHelper()->__($e->getMessage()));
        }
    }

    /**
     * Cancel order
     *
     * @param string $msg Order history message
     */
    protected function _processCancel($msg)
    {
        $this->_order->cancel();
        $this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $this->_getHelper()->__($msg));
        $this->_order->save();
    }

    /**
     * Invoice order
     *
     * @param string $msg Order history message
     */
    protected function _processSale($msg)
    {
        // invoice order
        if ($this->_order->canInvoice()) {
            $invoice = $this->_order->prepareInvoice();

            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        }

        // set new order state
        $newPaymentStatus = $this->_order->getPayment()->getMethodInstance()->getConfigData('order_status');
        if (empty($newPaymentStatus)) {
            $newPaymentStatus = Mage_Sales_Model_Order::STATE_PROCESSING;
        }
        $this->_order->setState($newPaymentStatus, $newPaymentStatus, $this->_getHelper()->__($msg));

        // save transaction ID
        $this->_order->getPayment()->setLastTransId($this->getRequest()->getParam('mb_transaction_id'));

        // send new order email
        $this->_order->sendNewOrderEmail();
        $this->_order->setEmailSent(true);

        $this->_order->save();
    }

    /**
     * Checking returned parameters
     *
     * @param bool $fullCheck Whether to make additional validations such as payment status, transaction signature etc.
     */
    protected function _checkReturnedData($fullCheck = true)
    {
        // get request variables
        $params = $this->getRequest()->getParams();
        if (empty($params)) {
            Mage::throwException('Request doesn\'t contain any elements.');
        }

        // check order ID
        if (empty($params['transaction_id'])
            || ($fullCheck == false && $this->_getCheckout()->getMoneybookersRealOrderId() != $params['transaction_id']))
        {
            Mage::throwException('Missing or invalid order ID.');
        }

        // load order for further validation
        $this->_order = Mage::getModel('sales/order')->loadByIncrementId($params['transaction_id']);
        if (!$this->_order->getId())
            Mage::throwException('Order not found.');

        // make additional validation
        if ($fullCheck) {
            // check payment status
            if (empty($params['status'])) {
                Mage::throwException('Unknown payment status');
            }

            // check transaction signature
            if (empty($params['md5sig'])) {
                Mage::throwException('Invalid transaction signature');
            }

            $checkParams = array('merchant_id', 'transaction_id', 'secret', 'mb_amount', 'mb_currency', 'status');
            $md5String = '';
            foreach ($checkParams as $key) {
                if ($key == 'merchant_id') {
                    $md5String .= Mage::getStoreConfig(Mage_Moneybookers_Helper_Data::XML_PATH_CUSTOMER_ID, $this->_order->getStoreId());
                } elseif ($key == 'secret') {
                    $md5String .= strtoupper(md5(Mage::getStoreConfig(Mage_Moneybookers_Helper_Data::XML_PATH_SECRET_KEY, $this->_order->getStoreId())));
                } elseif (isset($params[$key])) {
                    $md5String .= $params[$key];
                }
            }
            $md5String = strtoupper(md5($md5String));

            if ($md5String != $params['md5sig']) {
                Mage::throwException('Hash is not valid.');
            }

            // check transaction amount
            if (round($this->_order->getGrandTotal(), 2) != $params['mb_amount']) {
                Mage::throwException('Transaction amount doesn\'t match.');
            }

            // check transaction currency
            if ($this->_order->getOrderCurrencyCode() != $params['mb_currency']) {
                Mage::throwException('Transaction currency doesn\'t match.');
            }
        }

        return $params;
    }

    /**
     * Set redirect into responce. This has to be encapsulated in an JavaScript
     * call to jump out of the iframe.
     *
     * @param string $path
     * @param array $arguments
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('moneybookers/redirect')
                ->setRedirectUrl(Mage::getUrl($path, $arguments))
                ->toHtml()
        );
        return $this;
    }
}
