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
 * DirtectPost Payment Controller
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Directpost_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get session model

     * @return Mage_DirectPost_Model_Session
     */
    protected function _getDirectPostSession()
    {
        return Mage::getSingleton('authorizenet/directpost_session');
    }

    /**
     * Get iframe block instance
     *
     * @return Mage_DirectPost_Block_Iframe
     */
    protected function _getIframeBlock()
    {
        return $this->getLayout()->createBlock('directpost/iframe');
    }

    /**
     * Response action.
     * Action for Authorize.net SIM Relay Request.
     */
    public function responseAction()
    {
        $data = $this->getRequest()->getPost();
        /* @var $paymentMethod Mage_Authorizenet_Model_DirectPost */
        $paymentMethod = Mage::getModel('authorizenet/directpost');

        $result = array();
        if (!empty($data['x_invoice_num'])){
            $result['x_invoice_num'] = $data['x_invoice_num'];
        }

        try {
            $paymentMethod->process($data);
            $result['success'] = 1;
        }
        catch (Mage_Core_Exception $e){
            Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = $e->getMessage();
        }
        catch (Exception $e){
            Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }

        if (!empty($data['controller_action_name'])){
            if (!empty($data['key'])){
                $result['key'] = $data['key'];
            }
            $result['controller_action_name'] = $data['controller_action_name'];
            $params['redirect'] = Mage::helper('authorizenet')->getRedirectIframeUrl($result);
        }
        $block = $this->_getIframeBlock()->setParams($params);
        $this->getResponse()->setBody($block->toHtml());
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
            && isset($redirectParams['controller_action_name'])
        ){
            $this->_addAdditionalInformationToSession($redirectParams['x_invoice_num']);
            $this->_getDirectPostSession()->unsetData('quote_id');
            $params['redirect_parent'] = Mage::helper('authorizenet')->getSuccessOrderUrl($redirectParams);
        }
        if (!empty($redirectParams['error_msg'])) {
            $this->_returnCustomerQuote();
        }
        $block = $this->_getIframeBlock()->setParams(array_merge($params, $redirectParams));
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Send request to authorize.net
     *
     */
    public function placeAction()
    {
        $paymentParam = $this->getRequest()->getParam('payment');
        $controller = $this->getRequest()->getParam('controller');
        if (isset($paymentParam['method'])) {
            $saveOrderFlag = Mage::getStoreConfig('payment/'.$paymentParam['method'].'/create_order_before');
            if ($saveOrderFlag) {
                $params = Mage::helper('authorizenet')->getSaveOrderUrlParams($controller);
                $this->_getDirectPostSession()->setQuoteId($this->_getCheckout()->getQuote()->getId());
                $this->_forward(
                    $params['action'],
                    $params['controller'],
                    $params['module'],
                    $this->getRequest()->getParams()
                );
            }
            else {
                $this->_getCheckout()->getQuote()->getPayment()->importData($paymentParam);
                $quote = $this->_getCheckout()->getQuote();
                $payment = $quote->getPayment();
                if (!$quote->getReservedOrderId()) {
                    $quote->reserveOrderId()->save();
                }
                $this->_getDirectPostSession()->addCheckoutOrderIncrementId($quote->getReservedOrderId());
                $requestToPaygate = $payment->getMethodInstance()->generateRequestFromEntity($quote);
                $requestToPaygate->setControllerActionName($controller);
                $result = array(
                    'success'    => 1,
                    'directpost' => array('fields' => $requestToPaygate->getData())
                );
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
        else {
            $result = array(
                'error_messages' => $this->__('Please, choose payment method'),
                'goto_section'   => 'payment'
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Return customer quote by ajax
     *
     */
    public function returnQuoteAction()
    {
        if ($this->_returnCustomerQuote()) {
            $result = array('success' => 1);
        }
        else {
            $result = array('error_message' => $this->__('Can not return quote'));
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Return customer quote
     *
     * @return bool
     */
    protected function _returnCustomerQuote()
    {
        $quoteId = $this->_getDirectPostSession()->getQuoteId();
        $order = Mage::getModel('sales/order')->load($quoteId, 'quote_id');
        if ($order->getId() &&
            $this->_getDirectPostSession()
                ->isCheckoutOrderIncrementIdExist($order->getIncrementId())) {
            $quote = Mage::getModel('sales/quote')
                ->load($quoteId);
            if ($quote->getId()){
                $quote->setIsActive(1)
                    ->setReservedOrderId(NULL)
                    ->save();
                $this->_getCheckout()->replaceQuote($quote);
            }
            $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($order->getIncrementId());
            $this->_getDirectPostSession()->unsetData('quote_id');
            return true;
        }

        return false;
    }

    /**
     * Set additional information to session about order creation for 'order creation after payment' case.
     *
     * @param string $orderIncrementId
     */
    protected function _addAdditionalInformationToSession($orderIncrementId)
    {
        if ($orderIncrementId &&
            $this->_getDirectPostSession()
                    ->isCheckoutOrderIncrementIdExist($orderIncrementId)
        ){
            /* @var $quote Mage_Sales_Model_Quote */
            $quote = Mage::getModel('sales/quote')
                ->load($orderIncrementId, 'reserved_order_id');
            if ($quote->getId()){
                $payment = $quote->getPayment();
                if ($payment && $payment->getId() &&
                    $payment->getMethod() == Mage::getModel('authorizenet/directpost')->getCode()
                ){
                    $sessionData = $payment->getAdditionalInformation('session_data');
                    if ($sessionData){
                        foreach ($sessionData as $key => $val){
                            $this->_getCheckout()->setData($key, $val);
                        }
                    }
                }
            }
        }
    }
}
