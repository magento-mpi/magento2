<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class SaveShipping extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        $responseAjax = new \Magento\Framework\Object();

        try {
            $model = $this->_initModel();
            if ($model) {
                if ($this->labelService->createShippingLabel($model, $this->getRequest()->getPost())) {
                    $this->messageManager->addSuccess(__('You created a shipping label.'));
                    $responseAjax->setOk(true);
                }
                $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
            } else {
                $this->_forward('noroute');
                return;
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $responseAjax->setError(true);
            $responseAjax->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $responseAjax->setError(true);
            $responseAjax->setMessage(__('Something went wrong creating a shipping label.'));
        }
        $this->getResponse()->representJson($responseAjax->toJson());
    }
}
