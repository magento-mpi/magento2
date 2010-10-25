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
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Authorizenet directpayment observer
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Model_Directpost_Observer
{
    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Authorizenet_Model_Directpost_Observer
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('directpost_order', $order, true);

        return $this;
    }

    /**
     * Save need to notify order flag for admin order creation.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_DirectPayment_Model_Observer
     */
    public function saveNotifyFlag(Varien_Event_Observer $observer)
    {
        /* @var $controller Mage_Core_Controller_Varien_Action */
        $controller = $observer->getEvent()->getData('controller_action');
        $orderData = $controller->getRequest()->getPost('order');
        if ($orderData){
            $orderFlag = (!empty($orderData['send_confirmation'])) ? 1 : 0;
            $orderData['send_confirmation'] = 0;
            $controller->getRequest()->setPost('order', $orderData);
            Mage::register('directpost_order_notify', $orderFlag, true);
        }
        else {
            Mage::register('directpost_order_notify', 0, true);
        }
        return $this;
    }

    /**
     * Set data for response of admin saveOrder action.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Authorizenet_Model_Directpost_Observer
     */
    public function addAdditionalFieldsToResponseAdmin(Varien_Event_Observer $observer)
    {
        /* @var $controller Mage_Core_Controller_Varien_Action */
        $controller = $observer->getEvent()->getData('controller_action');
        if ($controller instanceof Mage_Adminhtml_Sales_Order_CreateController &&
            Mage::registry('authorizenet_method') == Mage::getModel('authorizenet/directpost')->getCode()
        ){
            $session = Mage::getSingleton('adminhtml/session_quote');
            $result = array('success' => 1);
            $controller->getResponse()->clearHeader('Location');
            if ($session->getMessages()->getErrors()){
                $result['success'] = 0;
                $result['error'] = 1;
                $result['redirect'] = Mage::getSingleton('adminhtml/url')->getUrl('*/*/');
            }
            else {
                /* @var $order Mage_Sales_Model_Order */
                $order = Mage::registry('directpost_order');

                if ($order && $order->getId()){
                    $payment = $order->getPayment();
                    if ($payment && $payment->getMethod() == 'authorizenet_directpost'){
                        //return json with data.
                        $session = Mage::getSingleton('authorizenet/directpost_session');
                        $session->addCheckoutOrderIncrementId($order->getIncrementId());

                        $requestToPaygate = $payment->getMethodInstance()->generateRequestFromEntity($order);
                        $requestToPaygate->setControllerActionName($controller->getRequest()->getControllerName());

                        $requestToPaygate->setOrderSendConfirmation(Mage::registry('directpost_order_notify'));

                        $this->_setSecretKey($requestToPaygate);

                        $result['directpost'] = array('fields' => $requestToPaygate->getData());
                    }
                }
            }
            $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Authorizenet_Model_Directpost_Observer
     */
    public function addAdditionalFieldsToResponseFrontend(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('directpost_order');

        if ($order && $order->getId()){
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == Mage::getModel('authorizenet/directpost')->getCode()){
                /* @var $controller Mage_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody('default'), Zend_Json::TYPE_ARRAY);

                if (empty($result['error'])){
                    $payment = $order->getPayment();
                    //if is success, then set order to session and add new fields
                    $session =  Mage::getSingleton('authorizenet/directpost_session');
                    $session->addCheckoutOrderIncrementId($order->getIncrementId());
                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromEntity($order);
                    $requestToPaygate->setControllerActionName($controller->getRequest()->getControllerName());
                    $result['directpost'] = array('fields' => $requestToPaygate->getData());
                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }

        return $this;
    }

    /**
     * Set key parameter for request for Admin area if needed.
     * Only for Admin area
     *
     * @param Mage_Authorizenet_Model_Directpost_Request $request
     */
    protected function _setSecretKey(Mage_Authorizenet_Model_Directpost_Request $request)
    {
        /* @var $adminUrl Mage_Adminhtml_Model_Url */
        $adminUrl = Mage::getSingleton('adminhtml/url');
        if ($adminUrl->useSecretKey()){
            $request->setKey($adminUrl->getSecretKey('authorizenet_directpost_payment', 'redirect'));
        }
    }

    /**
     * Add directpost payment form to revire step
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Authorizenet_Model_Directpost_Observer
     */
    public function addPaymentFormToReview(Varien_Event_Observer $observer)
    {
        $controller = $observer->getEvent()->getData('controller_action');
        $payment = $controller->getOnepage()->getQuote()->getPayment();
        if ($payment && $payment->getMethod() == Mage::getSingleton('authorizenet/directpost')->getCode()) {
            $result = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody('default'));
            if (empty($result['error'])){
                $block = $controller->getLayout()
                    ->createBlock($payment->getMethodInstance()->getFormBlockType())
                    ->setMethod($payment->getMethodInstance())
                    ->setTemplate('authorizenet/directpost/form.phtml');
                $result['update_section']['html'] .= $block->toHtml();
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }

        return $this;
    }
}
