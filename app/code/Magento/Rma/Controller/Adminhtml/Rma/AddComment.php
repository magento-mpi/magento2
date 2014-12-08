<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class AddComment extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Add RMA comment action
     *
     * @throws \Magento\Framework\Model\Exception
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initModel();

            $data = $this->getRequest()->getPost('comment');
            $notify = isset($data['is_customer_notified']);
            $visible = isset($data['is_visible_on_front']);

            $rma = $this->_coreRegistry->registry('current_rma');
            if (!$rma) {
                throw new \Magento\Framework\Model\Exception(__('Invalid RMA'));
            }

            $comment = trim($data['comment']);
            if (!$comment) {
                throw new \Magento\Framework\Model\Exception(__('Please enter a valid message.'));
            }
            /** @var $history \Magento\Rma\Model\Rma\Status\History */
            $history = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
            $history->setRma($rma);
            $history->setComment($comment);
            if ($notify) {
                $history->sendCommentEmail();
            }
            $history->saveComment($comment, $visible, true);

            $this->_view->loadLayout();
            $response = $this->_view->getLayout()->getBlock('comments_history')->toHtml();
        } catch (\Magento\Framework\Model\Exception $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('We cannot add the RMA history.')];
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
