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
 * @package     Mage_DirectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DirtectPayment Paygate Controller
 *
 * @category   Mage
 * @package    Mage_DirtectPayment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_DirectPayment_PaygateController extends Mage_Core_Controller_Front_Action
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

     * @return Mage_DirectPayment_Model_Session
     */
    protected function _getDirectPaymentSession()
    {
        return Mage::getSingleton('directpayment/session');
    }
    
    /**
     * Get iframe block instance
     *
     * @return Mage_DirectPayment_Block_Iframe
     */
    protected function _getIframeBlock()
    {
        return $this->getLayout()->createBlock('directpayment/iframe');
    }
        
    /**
     * Response action.
     * Action for Authorize.net SIM Relay Request.
     */
    public function responseAction()
    {
        $data = $this->getRequest()->getPost();
        /* @var $paymentMethod Mage_DirectPayment_Model_Authorizenet */
        $paymentMethod = Mage::getModel('directpayment/authorizenet');
        
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
            $params['redirect'] = Mage::helper('directpayment')->getRedirectIframeUrl($result);
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
            && isset($redirectParams['controller_action_name'])) {
            $params['redirect_parent'] = Mage::helper('directpayment')->getSuccessOrderUrl($redirectParams);
        }
        if (!empty($redirectParams['error_msg'])
            && isset($redirectParams['x_invoice_num'])) {
            $this->_returnCustomerQuote($redirectParams['x_invoice_num']);
        }
        $block = $this->_getIframeBlock()->setParams(array_merge($params, $redirectParams));
        $this->getResponse()->setBody($block->toHtml());
    }
    
    /**
     * Place order before payment
     *
     */
    public function placeAction()
    {
        $payment = $this->getRequest()->getParam('payment');
        $controller = $this->getRequest()->getParam('controller');
        if (isset($payment['method'])) {
            $saveOrderFlag = Mage::getStoreConfig('payment/'.$payment['method'].'/create_order_before');
            if ($saveOrderFlag) {
                $params = Mage::helper('directpayment')->getSaveOrderUrlParams($controller);
                $this->_forward(
                            $params['action'],
                            $params['controller'],
                            $params['module'],
                            $this->getRequest()->getParams()
                );
            }
            else {
                //TODO: place order, not save
            }
        }
    }
    
    /**
     * Return customer quote
     *
     * @param int $orderIncrementId
     * @return bool
     */
    protected function _returnCustomerQuote($orderIncrementId)
    {
        if ($orderIncrementId &&
            $this->_getDirectPaymentSession()
                    ->isCheckoutOrderIncrementIdExist($orderIncrementId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            if ($order->getId()) {
                $quoteId = $order->getQuoteId();
                if ($quoteId) {
                    $quote = Mage::getModel('sales/quote')
                        ->load($quoteId);
                    if ($quote->getId()){
                        $quote->setIsActive(1)
                            ->setReservedOrderId(NULL)
                            ->save();
                        $this->_getCheckout()->replaceQuote($quote);
                    }
                }
            }
            $this->_getDirectPaymentSession()->removeCheckoutOrderIncrementId($orderIncrementId);
            return true;
        }
        
        return false;
    }
}
