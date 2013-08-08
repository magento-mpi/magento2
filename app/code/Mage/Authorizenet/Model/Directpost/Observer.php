<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
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
     * @param Magento_Event_Observer $observer
     * @return Mage_Authorizenet_Model_Directpost_Observer
     */
    public function saveOrderAfterSubmit(Magento_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('directpost_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Authorizenet_Model_Directpost_Observer
     */
    public function addAdditionalFieldsToResponseFrontend(Magento_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('directpost_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == Mage::getModel('Mage_Authorizenet_Model_Directpost')->getCode()) {
                /* @var $controller Mage_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = Mage::helper('Mage_Core_Helper_Data')->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
                );

                if (empty($result['error'])) {
                    $payment = $order->getPayment();
                    //if success, then set order to session and add new fields
                    $session = Mage::getSingleton('Mage_Authorizenet_Model_Directpost_Session');
                    $session->addCheckoutOrderIncrementId($order->getIncrementId());
                    $session->setLastOrderIncrementId($order->getIncrementId());
                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                    $requestToPaygate->setControllerActionName($controller->getRequest()->getControllerName());
                    $requestToPaygate->setIsSecure((string)Mage::app()->getStore()->isCurrentlySecure());

                    $result['directpost'] = array('fields' => $requestToPaygate->getData());

                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
                }
            }
        }

        return $this;
    }

    /**
     * Update all edit increments for all orders if module is enabled.
     * Needed for correct work of edit orders in Admin area.
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Authorizenet_Model_Directpost_Observer
     */
    public function updateAllEditIncrements(Magento_Event_Observer $observer)
    {
         /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::helper('Mage_Authorizenet_Helper_Data')->updateOrderEditIncrements($order);

        return $this;
    }
}
