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
namespace Magento\Authorizenet\Model\Directpost;

class Observer
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Authorizenet data
     *
     * @var \Magento\Authorizenet\Helper\Data
     */
    protected $_authorizenetData = null;

    /**
     * @param \Magento\Authorizenet\Helper\Data $authorizenetData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Authorizenet\Helper\Data $authorizenetData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;

        $this->_authorizenetData = $authorizenetData;
        $this->_coreData = $coreData;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Authorizenet\Model\Directpost\Observer
     */
    public function saveOrderAfterSubmit(\Magento\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getData('order');
        $this->_coreRegistry->register('directpost_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Authorizenet\Model\Directpost\Observer
     */
    public function addAdditionalFieldsToResponseFrontend(\Magento\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $this->_coreRegistry->registry('directpost_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == \Mage::getModel('Magento\Authorizenet\Model\Directpost')->getCode()) {
                /* @var $controller \Magento\Core\Controller\Varien\Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = $this->_coreData->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    \Zend_Json::TYPE_ARRAY
                );

                if (empty($result['error'])) {
                    $payment = $order->getPayment();
                    //if success, then set order to session and add new fields
                    $session = \Mage::getSingleton('Magento\Authorizenet\Model\Directpost\Session');
                    $session->addCheckoutOrderIncrementId($order->getIncrementId());
                    $session->setLastOrderIncrementId($order->getIncrementId());
                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                    $requestToPaygate->setControllerActionName($controller->getRequest()->getControllerName());
                    $requestToPaygate->setIsSecure((string)\Mage::app()->getStore()->isCurrentlySecure());

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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Authorizenet\Model\Directpost\Observer
     */
    public function updateAllEditIncrements(\Magento\Event\Observer $observer)
    {
         /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getData('order');
        $this->_authorizenetData->updateOrderEditIncrements($order);

        return $this;
    }
}
