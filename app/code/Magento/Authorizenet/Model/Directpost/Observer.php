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
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;
    
    /**
     * Core helper
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * Authorizenet helper
     *
     * @var Magento_Authorizenet_Helper_Data
     */
    protected $_authorizenetData;

    /**
     * @var Magento_Authorizenet_Model_DirectpostFactory
     */
    protected $_modelFactory;

    /**
     * @var Magento_Authorizenet_Model_Directpost_Session
     */
    protected $_session;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Authorizenet_Helper_Data $authorizenetData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Authorizenet_Model_DirectpostFactory $modelFactory
     * @param Magento_Authorizenet_Model_Directpost_Session $session
     * @param Magento_Core_Model_StoreManager $storeManager
     */
    public function __construct(
        Magento_Authorizenet_Helper_Data $authorizenetData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Authorizenet_Model_DirectpostFactory $modelFactory,
        Magento_Authorizenet_Model_Directpost_Session $session,
        Magento_Core_Model_StoreManager $storeManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_authorizenetData = $authorizenetData;
        $this->_coreData = $coreData;
        $this->_modelFactory = $modelFactory;
        $this->_session = $session;
        $this->_storeManager = $storeManager;
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
        $this->_coreRegistry->register('directpost_order', $order, true);

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
        $order = $this->_coreRegistry->registry('directpost_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == $this->_modelFactory->create()->getCode()) {
                /* @var $controller Magento_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = $this->_coreData->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
                );

                if (empty($result['error'])) {
                    $payment = $order->getPayment();
                    //if success, then set order to session and add new fields
                    $this->_session->addCheckoutOrderIncrementId($order->getIncrementId());
                    $this->_session->setLastOrderIncrementId($order->getIncrementId());
                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                    $requestToPaygate->setControllerActionName($controller->getRequest()->getControllerName());
                    $requestToPaygate->setIsSecure((string)$this->_storeManager->getStore()->isCurrentlySecure());

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
