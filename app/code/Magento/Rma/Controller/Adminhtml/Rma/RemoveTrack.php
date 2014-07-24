<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class RemoveTrack extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Remove tracking number from shipment
     *
     * @return void
     */
    public function execute()
    {
        $trackId = $this->getRequest()->getParam('track_id');
        /** @var $shippingModel \Magento\Rma\Model\Shipping */
        $shippingModel = $this->_objectManager->create('Magento\Rma\Model\Shipping');
        $shippingModel->load($trackId);
        if ($shippingModel->getId()) {
            try {
                $model = $this->_initModel();
                if ($model->getId()) {
                    $shippingModel->delete();

                    $this->_view->loadLayout();
                    $response = $this->_view->getLayout()->getBlock('shipment_tracking')->toHtml();
                } else {
                    $response = array(
                        'error' => true,
                        'message' => __('We cannot initialize an RMA to delete a tracking number.')
                    );
                }
            } catch (\Exception $e) {
                $response = array('error' => true, 'message' => __('We cannot delete the tracking number.'));
            }
        } else {
            $response = array('error' => true, 'message' => __('We cannot load track with retrieving identifier.'));
        }
        if (is_array($response)) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response)
            );
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
