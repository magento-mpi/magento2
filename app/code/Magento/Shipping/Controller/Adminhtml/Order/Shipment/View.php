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

class View extends \Magento\Backend\App\Action
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
     * Shipment information page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Shipments'));
        $shipment = $this->shipmentLoader->load($this->_request);
        if ($shipment) {
            $this->_title->add("#" . $shipment->getIncrementId());
            $this->_view->loadLayout();
            $this->_view->getLayout()->getBlock(
                'sales_shipment_view'
            )->updateBackButtonUrl(
                $this->getRequest()->getParam('come_from')
            );
            $this->_setActiveMenu('Magento_Sales::sales_order');
            $this->_view->renderLayout();
        } else {
            $this->_forward('noroute');
        }
    }
}
