<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class Save extends SaveNew
{
    /**
     * Save RMA request
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        $rmaId = (int)$this->getRequest()->getParam('rma_id');
        if (!$rmaId) {
            parent::execute();
            return;
        }
        try {
            $saveRequest = $this->rmaDataMapper->filterRmaSaveRequest($this->getRequest()->getPost());
            $itemStatuses = $this->rmaDataMapper->combineItemStatuses($saveRequest['items'], $rmaId);
            $model = $this->_initModel('rma_id');
            /** @var $sourceStatus \Magento\Rma\Model\Rma\Source\Status */
            $sourceStatus = $this->_objectManager->create('Magento\Rma\Model\Rma\Source\Status');
            $model->setStatus($sourceStatus->getStatusByItems($itemStatuses))->setIsUpdate(1);
            if (!$model->saveRma($saveRequest)) {
                throw new \Magento\Framework\Model\Exception(__('We failed to save this RMA.'));
            }
            /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
            $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
            $statusHistory->setRma($model);
            $statusHistory->sendAuthorizeEmail();
            $statusHistory->saveSystemComment();
            $this->messageManager->addSuccess(__('You saved the RMA request.'));
            $redirectBack = $this->getRequest()->getParam('back', false);
            if ($redirectBack) {
                $this->_redirect('adminhtml/*/edit', ['id' => $rmaId, 'store' => $model->getStoreId()]);
                return;
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $errorKeys = $this->_objectManager->get('Magento\Framework\Session\Generic')->getRmaErrorKeys();
            $controllerParams = ['id' => $rmaId];
            if (isset($errorKeys['tabs']) && $errorKeys['tabs'] == 'items_section') {
                $controllerParams['active_tab'] = 'items_section';
            }
            $this->_redirect('adminhtml/*/edit', $controllerParams);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We failed to save this RMA.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_redirect('adminhtml/*/');
            return;
        }
        $this->_redirect('adminhtml/*/');
    }
}
