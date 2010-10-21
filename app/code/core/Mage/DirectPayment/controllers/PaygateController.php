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
        if (!empty($data['controller_action_name'])){
            $result['controller_action_name'] = $data['controller_action_name'];
        }
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
        
        $params['redirect'] = Mage::getUrl('directpayment/paygate/redirect', $result);
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
        $block = $this->_getIframeBlock()->setParams(array_merge($params, $redirectParams));
        $this->getResponse()->setBody($block->toHtml());
    }
    
    /**
     * Check order responce status and place or cancel order
     *
     */
    public function placeAction()
    {
        $orderIncrementId = $this->getRequest()->getPost('orderIncrementId');
        $result = array();
        if ($orderIncrementId && $this->_getDirectPaymentSession()->isCheckoutOrderIncrementIdExist($orderIncrementId)) {
            //check request from authorize.net in db
            if (true) {
                //TODO:change order status
                $result['success'] = 1;
                $result['redirect'] = 1;
                $result['redirectUrl'] = Mage::getUrl('checkout/onepage/success');
            }
            else {
               $result['error_messages'] = 'Payment Error';
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    /**
     * Cancel wrong order and return quote to customer.
     *
     * @param int $orderId
     * @return bool
     */
    protected function _cancelOrder($orderIncrementId)
    {
        $orderIncrementId = $this->getRequest()->getPost('orderIncrementId');
        $result = array();
        if ($orderIncrementId && $this->_getDirectPaymentSession()->isCheckoutOrderIncrementIdExist($orderIncrementId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            if ($order->getId()) {
                //check if order exists and assigned to
                $quoteId = $order->getQuoteId();
                $order->cancel()
                    ->save();
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
