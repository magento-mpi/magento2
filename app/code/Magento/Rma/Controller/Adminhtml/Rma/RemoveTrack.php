<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        try {
            $model = $this->_initModel();
            if ($model->getId()) {
                $this->labelService->removeTrack($trackId, $model->getId());
                $this->_view->loadLayout();
                $response = $this->_view->getLayout()->getBlock('shipment_tracking')->toHtml();
            } else {
                $response = [
                    'error' => true,
                    'message' => __('We cannot initialize an RMA to delete a tracking number.'),
                ];
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $response = ['error' => true, 'message' => __('We cannot delete the tracking number.')];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
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
