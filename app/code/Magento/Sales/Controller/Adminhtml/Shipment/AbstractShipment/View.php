<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Shipment\AbstractShipment;

abstract class View extends \Magento\Backend\App\Action
{
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
        if ($shipmentId = $this->getRequest()->getParam('shipment_id')) {
            $this->_forward('view', 'order_shipment', 'admin', array('come_from' => 'shipment'));
        } else {
            $this->_forward('noroute');
        }
    }
}
