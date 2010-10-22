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
 * @package     Mage_Authorizenet
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admihtml DirtectPost Payment Controller
 *
 * @category   Mage
 * @package    Mage_DirtectPost
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Adminhtml_Authorizenet_Directpost_PaymentController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Get session model
     *
     * @return Mage_DirectPost_Model_Session
     */
    protected function _getDirectPostSession()
    {
        return Mage::getSingleton('authorizenet/directpost_session');
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getOrderSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Send request to authorize.net
     *
     */
    public function placeAction()
    {
        $paymentParam = $this->getRequest()->getParam('payment');
        $controller = $this->getRequest()->getParam('controller');
        Mage::register('authorizenet_method', Mage::getModel('authorizenet/directpost')->getCode(), true);
        if (isset($paymentParam['method'])) {
            $saveOrderFlag = Mage::getStoreConfig('payment/'.$payment['method'].'/create_order_before');
            if ($saveOrderFlag) {
                $params = Mage::helper('authorizenet')->getSaveOrderUrlParams($controller);
                $this->_forward(
                            $params['action'],
                            $params['controller'],
                            $params['module'],
                            $this->getRequest()->getParams()
                );
            }
            else {
                $this->_getOrderCreateModel()->setPaymentData($paymentParam);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentParam);
                $quote = $this->_getOrderCreateModel()->getQuote();
                $payment = $quote->getPayment();
                if (!$quote->getReservedOrderId()) {
                    $quote->reserveOrderId()->save();
                }
                $this->_getDirectPostSession()->addCheckoutOrderIncrementId($quote->getReservedOrderId());
                $requestToPaygate = $payment->getMethodInstance()->generateRequestFromQuote($quote);
                $requestToPaygate->setControllerActionName($controller);            
                $requestToPaygate->setOrderSendConfirmation(0);                
                $result = array(
                    'success'    => 1,
                    'directpost' => array('fields' => $requestToPaygate->getData())
                );
            }
        }
        else {
            $result = array(
            	'error_messages' => $this->__('Please, choose payment method')                
            );
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Retrieve params and put javascript into iframe
     *
     */
    public function redirectAction()
    {
        $redirectParams = $this->getRequest()->getParams();
        $params = array();
        if (!empty($redirectParams['success'])
            && isset($redirectParams['x_invoice_num'])
            && isset($redirectParams['controller_action_name'])) {
            $params['redirect_parent'] = Mage::helper('authorizenet')->getSuccessOrderUrl($redirectParams);
        }
        if (!empty($redirectParams['error_msg'])
            && isset($redirectParams['x_invoice_num'])) {
            $this->_returnQuote($redirectParams['x_invoice_num']);
        }
        $block = $this->getLayout()
                        ->createBlock('directpost/iframe')
                        ->setParams(array_merge($params, $redirectParams));
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Return quote
     *
     * @param int $orderIncrementId
     * @return bool
     */
    protected function _returnQuote($orderIncrementId)
    {
        if ($orderIncrementId &&
            $this->_getDirectPostSession()
                    ->isCheckoutOrderIncrementIdExist($orderIncrementId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            if ($order->getId()) {
                $quoteId = $order->getQuoteId();
                $order->setReordered(true);
                $this->_getOrderSession()->setUseOldShippingMethod(true);
                $this->_getOrderCreateModel()->initFromOrder($order);
            }
            $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($orderIncrementId);
            return true;
        }

        return false;
    }
}
