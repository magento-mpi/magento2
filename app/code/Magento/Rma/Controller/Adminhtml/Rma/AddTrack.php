<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class AddTrack extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Add new tracking number action
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number = $this->getRequest()->getPost('number');
            $title = $this->getRequest()->getPost('title');
            if (empty($carrier)) {
                throw new \Magento\Framework\Model\Exception(__('Please specify a carrier.'));
            }
            if (empty($number)) {
                throw new \Magento\Framework\Model\Exception(__('You need to enter a tracking number.'));
            }

            $model = $this->_initModel();
            if ($model->getId()) {
                $this->labelService->addTrack($model->getId(), $number, $carrier, $title);
                $this->_view->loadLayout();
                $response = $this->_view->getLayout()->getBlock('shipment_tracking')->toHtml();
            } else {
                $response = array(
                    'error' => true,
                    'message' => __('We cannot initialize an RMA to add a tracking number.')
                );
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (\Exception $e) {
            $response = array('error' => true, 'message' => __('We cannot add a message.'));
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
