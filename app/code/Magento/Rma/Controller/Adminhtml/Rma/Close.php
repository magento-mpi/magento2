<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class Close extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Close action for rma
     *
     * @return void
     */
    public function execute()
    {
        $entityId = $this->getRequest()->getParam('entity_id');
        if ($entityId) {
            $entityId = intval($entityId);
            $entityIds = array($entityId);
            $returnRma = $entityId;
        } else {
            $entityIds = $this->getRequest()->getPost('entity_ids', array());
            $returnRma = null;
        }
        $countCloseRma = 0;
        $countNonCloseRma = 0;
        foreach ($entityIds as $entityId) {
            /** @var $rma \Magento\Rma\Model\Rma */
            $rma = $this->_objectManager->create('Magento\Rma\Model\Rma')->load($entityId);
            if ($rma->canClose()) {
                $rma->close()->save();
                /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                $statusHistory->setRma($rma);
                $statusHistory->saveSystemComment();
                $countCloseRma++;
            } else {
                $countNonCloseRma++;
            }
        }
        if ($countNonCloseRma) {
            if ($countCloseRma) {
                $this->messageManager->addError(__('%1 RMA(s) cannot be closed', $countNonCloseRma));
            } else {
                $this->messageManager->addError(__('We cannot close the RMA request(s).'));
            }
        }
        if ($countCloseRma) {
            $this->messageManager->addSuccess(__('%1 RMA (s) have been closed.', $countCloseRma));
        }

        if ($returnRma) {
            $this->_redirect('adminhtml/*/edit', array('id' => $returnRma));
        } else {
            $this->_redirect('adminhtml/*/');
        }
    }
}
