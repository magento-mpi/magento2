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

class RemoveTrack extends \Magento\Backend\App\Action
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
     * Remove tracking number from shipment
     *
     * @return void
     */
    public function execute()
    {
        $trackId = $this->getRequest()->getParam('track_id');
        $track = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment\Track')->load($trackId);
        if ($track->getId()) {
            try {
                $this->_title->add(__('Shipments'));
                $shipment = $this->shipmentLoader->load($this->_request);
                if ($shipment) {
                    $track->delete();

                    $this->_view->loadLayout();
                    $response = $this->_view->getLayout()->getBlock('shipment_tracking')->toHtml();
                } else {
                    $response = array(
                        'error' => true,
                        'message' => __('Cannot initialize shipment for delete tracking number.')
                    );
                }
            } catch (\Exception $e) {
                $response = array('error' => true, 'message' => __('Cannot delete tracking number.'));
            }
        } else {
            $response = array('error' => true, 'message' => __('Cannot load track with retrieving identifier.'));
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
