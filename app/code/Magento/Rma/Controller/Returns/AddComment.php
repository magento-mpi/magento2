<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Returns;

use Magento\Rma\Model\Rma;

class AddComment extends \Magento\Rma\Controller\Returns
{
    /**
     * Add RMA comment action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_loadValidRma()) {
            return;
        }
        try {
            $comment = $this->getRequest()->getPost('comment');
            $comment = trim(strip_tags($comment));
            if (empty($comment)) {
                throw new \Magento\Framework\Model\Exception(__('Please enter a valid message.'));
            }
            /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
            $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
            $statusHistory->setRma($this->_coreRegistry->registry('current_rma'));
            $statusHistory->setComment($comment);
            $statusHistory->sendCustomerCommentEmail();
            $statusHistory->saveComment($comment, true, false);
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Cannot add message.'));
        }
        $this->_redirect('*/*/view', ['entity_id' => (int) $this->getRequest()->getParam('entity_id')]);
    }
}
