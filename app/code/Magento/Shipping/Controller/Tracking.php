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

namespace Magento\Shipping\Controller;

use Magento\App\Action\NotFoundException;

class Tracking extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Shipping\Model\InfoFactory
     */
    protected $_shippingInfoFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Shipping\Model\Resource\Order\Track\CollectionFactory
     */
    protected $_trackCollectionFactory;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Shipping\Model\InfoFactory $shippingInfoFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Shipping\Model\Resource\Order\Track\CollectionFactory $trackCollectionFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Shipping\Model\InfoFactory $shippingInfoFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Shipping\Model\Resource\Order\Track\CollectionFactory $trackCollectionFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_shippingInfoFactory = $shippingInfoFactory;
        $this->_orderFactory = $orderFactory;
        $this->_trackCollectionFactory = $trackCollectionFactory;
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
            $tracks = $this->_getTracksCollection($order);

            $block = $this->_objectManager->create('Magento\View\Element\Template');
            $block->setType('Magento\View\Element\Template')
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
     *
     * @throws NotFoundException
     */
    public function popupAction()
    {
        $shippingInfoModel = $this->_shippingInfoFactory->create()->loadByHash($this->getRequest()->getParam('hash'));
        $this->_coreRegistry->register('current_shipping_info', $shippingInfoModel);
        if (count($shippingInfoModel->getTrackingInfo()) == 0) {
            throw new NotFoundException();
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }


    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Model\Order || false
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

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return mixed
     */
    protected function _getTracksCollection(\Magento\Sales\Model\Order $order)
    {
        /** @var \Magento\Shipping\Model\Resource\Order\Track\Collection $tracks */
        $tracks = $this->_trackCollectionFactory->create()->setOrderFilter($order);

        if ($order->getId()) {
            $tracks->load();
        }

        return $tracks;
    }
}
