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
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Authorizenet data
     *
     * @var Magento_Authorizenet_Helper_Data
     */
    protected $_authorizenetData = null;

    /**
     * @param Magento_Authorizenet_Helper_Data $authorizenetData
     * @param Magento_Core_Helper_Data $coreData
     */
    public function __construct(
        Magento_Authorizenet_Helper_Data $authorizenetData,
        Magento_Core_Helper_Data $coreData
    ) {
        $this->_authorizenetData = $authorizenetData;
        $this->_coreData = $coreData;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Authorizenet_Model_Directpost_Observer
     */
    public function saveOrderAfterSubmit(Magento_Event_Observer $observer)
    {
        /* @var $order Magento_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('directpost_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Authorizenet_Model_Directpost_Observer
     */
    public function addAdditionalFieldsToResponseFrontend(Magento_Event_Observer $observer)
    {
        /* @var $order Magento_Sales_Model_Order */
        $order = Mage::registry('directpost_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == Mage::getModel('Magento_Authorizenet_Model_Directpost')->getCode()) {
                /* @var $controller Magento_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = $this->_coreData->jsonDecode(
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
                    $controller->getResponse()->setBody($this->_coreData->jsonEncode($result));
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
     * @return Magento_Authorizenet_Model_Directpost_Observer
     */
    public function updateAllEditIncrements(Magento_Event_Observer $observer)
    {
         /* @var $order Magento_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        $this->_authorizenetData->updateOrderEditIncrements($order);

        return $this;
    }
}
