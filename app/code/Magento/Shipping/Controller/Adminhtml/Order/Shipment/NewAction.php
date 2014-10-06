<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Controller\Adminhtml\Order\Shipment;

use \Magento\Backend\App\Action;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @param Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        Action\Context $context,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
    ) {
        $this->shipmentLoader = $shipmentLoader;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }

    /**
     * Shipment create page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Shipments'));
        $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
        $shipment = $this->shipmentLoader->load();
        if ($shipment) {
            $this->_title->add(__('New Shipment'));

            $comment = $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
            if ($comment) {
                $shipment->setCommentText($comment);
            }

            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_Sales::sales_order');
            $this->_view->renderLayout();
        } else {
            $this->_redirect('*/order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
        }
    }
}
