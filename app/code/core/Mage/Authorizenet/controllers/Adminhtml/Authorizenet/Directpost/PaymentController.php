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
            $saveOrderFlag = Mage::getStoreConfig('payment/'.$paymentParam['method'].'/create_order_before');
            if ($saveOrderFlag) {
                $params = Mage::helper('authorizenet')->getSaveOrderUrlParams($controller);
                $this->_getDirectPostSession()->setQuoteId($this->_getOrderSession()->getQuote()->getId());
                $this->_forward(
                    $params['action'],
                    $params['controller'],
                    $params['module'],
                    $this->getRequest()->getParams()
                );
            }
            else {
                $quote = $this->_getOrderCreateModel()->getQuote();
                $quote->getPayment()->importData($paymentParam);
                $payment = $quote->getPayment();
                if (!$quote->getReservedOrderId()) {
                    $quote->reserveOrderId()->save();
                }
                $this->_getDirectPostSession()->addCheckoutOrderIncrementId($quote->getReservedOrderId());
                $requestToPaygate = $payment->getMethodInstance()->generateRequestFromEntity($quote);
                $requestToPaygate->setControllerActionName($controller);
                $requestToPaygate->setOrderSendConfirmation(Mage::registry('directpost_order_notify'));
                $adminUrl = Mage::getSingleton('adminhtml/url');
                if ($adminUrl->useSecretKey()){
                    $requestToPaygate->setKey($adminUrl->getSecretKey('authorizenet_directpost_payment', 'redirect'));
                }
                $result = array(
                    'success'    => 1,
                    'directpost' => array('fields' => $requestToPaygate->getData())
                );
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
        else {
            $result = array(
                'error_messages' => $this->__('Please, choose payment method')
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
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
            $this->_getDirectPostSession()->unsetData('quote_id');
            $this->_getSession()->clear();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been created.'));
        }
        if (!empty($redirectParams['error_msg'])) {
            $cancelOrder = empty($redirectParams['x_invoice_num']);
            $this->_returnQuote($cancelOrder, $redirectParams['error_msg']);
            $this->_getSession()->addError($redirectParams['error_msg']);
        }
        $block = $this->getLayout()
            ->createBlock('directpost/iframe')
            ->setParams(array_merge($params, $redirectParams));
        $this->getResponse()->setBody($block->toHtml());
    }

	/**
     * Return order quote by ajax
     *
     */
    public function returnQuoteAction()
    {
        if ($this->_returnQuote()) {
            $result = array('success' => 1);
        }
        else {
            $result = array('error_message' => $this->__('Payment transaction error.'));
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Return quote
     * 
     * @param bool $cancelOrder
     * @param string $errorMsg
     * @return bool
     */
    protected function _returnQuote($cancelOrder = false, $errorMsg = '')
    {        
        $incrementId = $this->_getDirectPostSession()->getLastOrderIncrementId();        
        if ($incrementId &&
            $this->_getDirectPostSession()
                ->isCheckoutOrderIncrementIdExist($incrementId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
            if ($order->getId()) { 
                $order->setReordered(true);
                $this->_getOrderSession()->setUseOldShippingMethod(true);
                $this->_getOrderCreateModel()->initFromOrder($order);
                $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($order->getIncrementId());
                $this->_getDirectPostSession()->unsetData('quote_id');
                if ($cancelOrder) {
                    $order->registerCancellation($errorMsg)->save();
                }
                return true;
            }
        }

        return false;
    }
}
