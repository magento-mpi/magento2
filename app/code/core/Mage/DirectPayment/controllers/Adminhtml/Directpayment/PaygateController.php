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
 * @package     Mage_DirtectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admihtml DirtectPayment Paygate Controller
 *
 * @category   Mage
 * @package    Mage_DirtectPayment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_DirectPayment_Adminhtml_Directpayment_PaygateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Get session model
     * 
     * @return Mage_DirectPayment_Model_Session
     */
    protected function _getDirectPaymentSession()
    {
        return Mage::getSingleton('directpayment/session');
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
     * Retrieve params and put javascript into iframe
     *
     */
    public function redirectAction()
    {
        $redirectParams = $this->getRequest()->getParams();Mage::log($redirectParams);
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
        $block = $this->getLayout()
                        ->createBlock('directpayment/iframe')
                        ->setParams(array_merge($params, $redirectParams));
        $this->getResponse()->setBody($block->toHtml());
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
                $order->setReordered(true);
                $this->_getOrderSession()->setUseOldShippingMethod(true);
                $this->_getOrderCreateModel()->initFromOrder($order);
            }
            $this->_getDirectPaymentSession()->removeCheckoutOrderIncrementId($orderIncrementId);
            return true;
        }
        
        return false;
    }
}
