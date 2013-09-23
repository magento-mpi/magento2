<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders controller
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Shipping_Controller_Tracking extends Magento_Core_Controller_Front_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Shipping_Model_InfoFactory
     */
    protected $_shippingInfoFactory;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Shipping_Model_InfoFactory $shippingInfoFactory
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Customer_Model_Session $customerSession,
        Magento_Shipping_Model_InfoFactory $shippingInfoFactory,
        Magento_Sales_Model_OrderFactory $orderFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_shippingInfoFactory = $shippingInfoFactory;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context);
    }

    /**
     * Ajax action
     *
     */
    public function ajaxAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            $response = '';
            $tracks = $order->getTracksCollection();

            $block = $this->_objectManager->create('Magento_Core_Block_Template');
            $block->setType('Magento_Core_Block_Template')
                ->setTemplate('order/trackinginfo.phtml');

            foreach ($tracks as $track) {
                $trackingInfo = $track->getNumberDetail();
                $block->setTrackingInfo($trackingInfo);
                $response .= $block->toHtml()."\n<br />";
            }

            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Popup action
     * Shows tracking info if it's present, otherwise redirects to 404
     */
    public function popupAction()
    {
        $shippingInfoModel = $this->_shippingInfoFactory->create()->loadByHash($this->getRequest()->getParam('hash'));
        $this->_coreRegistry->register('current_shipping_info', $shippingInfoModel);
        if (count($shippingInfoModel->getTrackingInfo()) == 0) {
            $this->norouteAction();
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }


    /**
     * Initialize order model instance
     *
     * @return Magento_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');

        $order = $this->_orderFactory->create()->load($id);
        $customerId = $this->_customerSession->getCustomerId();

        if (!$order->getId() || !$customerId || $order->getCustomerId() != $customerId) {
            return false;
        }
        return $order;
    }

}
