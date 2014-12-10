<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Guest;

use Magento\Rma\Model\Rma;

class AddComment extends \Magento\Rma\Controller\Guest
{
    /**
     * Add RMA comment action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_loadValidRma()) {
            try {
                $response = false;
                $comment = $this->getRequest()->getPost('comment');
                $comment = trim(strip_tags($comment));

                if (!empty($comment)) {
                    /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                    $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                    $statusHistory->setRma($this->_coreRegistry->registry('current_rma'));
                    $statusHistory->setComment($comment);
                    $statusHistory->sendCustomerCommentEmail();
                    $statusHistory->saveComment($comment, true, false);
                } else {
                    throw new \Magento\Framework\Model\Exception(__('Please enter a valid message.'));
                }
            } catch (\Magento\Framework\Model\Exception $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot add a message.')];
            }
            if (is_array($response)) {
                $this->messageManager->addError($response['message']);
            }
            $this->_redirect('*/*/view', ['entity_id' => (int)$this->getRequest()->getParam('entity_id')]);
            return;
        }
        return;
    }
}
