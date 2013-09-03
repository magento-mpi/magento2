<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorizenet directpayment observer
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Authorizenet_Model_Directpost_Observer
{
    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Authorizenet_Model_Directpost_Observer
     */
    public function saveOrderAfterSubmit(\Magento\Event\Observer $observer)
    {
        /* @var $order Magento_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('directpost_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Authorizenet_Model_Directpost_Observer
     */
    public function addAdditionalFieldsToResponseFrontend(\Magento\Event\Observer $observer)
    {
        /* @var $order Magento_Sales_Model_Order */
        $order = Mage::registry('directpost_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == Mage::getModel('Magento_Authorizenet_Model_Directpost')->getCode()) {
                /* @var $controller Magento_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = Mage::helper('Magento_Core_Helper_Data')->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
                );

                if (empty($result['error'])) {
                    $payment = $order->getPayment();
                    //if success, then set order to session and add new fields
                    $session = Mage::getSingleton('Magento_Authorizenet_Model_Directpost_Session');
                    $session->addCheckoutOrderIncrementId($order->getIncrementId());
                    $session->setLastOrderIncrementId($order->getIncrementId());
                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                    $requestToPaygate->setControllerActionName($controller->getRequest()->getControllerName());
                    $requestToPaygate->setIsSecure((string)Mage::app()->getStore()->isCurrentlySecure());

                    $result['directpost'] = array('fields' => $requestToPaygate->getData());

                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->setBody(Mage::helper('Magento_Core_Helper_Data')->jsonEncode($result));
                }
            }
        }

        return $this;
    }

    /**
     * Update all edit increments for all orders if module is enabled.
     * Needed for correct work of edit orders in Admin area.
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Authorizenet_Model_Directpost_Observer
     */
    public function updateAllEditIncrements(\Magento\Event\Observer $observer)
    {
         /* @var $order Magento_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::helper('Magento_Authorizenet_Helper_Data')->updateOrderEditIncrements($order);

        return $this;
    }
}
