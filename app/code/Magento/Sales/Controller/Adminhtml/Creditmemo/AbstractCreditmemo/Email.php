<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo;

use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

class Email extends \Magento\Backend\App\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }

    /**
     * Notify user
     *
     * @return void
     */
    public function execute()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if (!$creditmemoId) {
            return;
        }
        $creditmemo = $this->_objectManager->create('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
        if (!$creditmemo) {
            return;
        }
        $this->_objectManager->create('Magento\Sales\Model\CreditmemoNotifier')
            ->notify($creditmemo);

        $this->messageManager->addSuccess(__('We sent the message.'));
        $this->_redirect('sales/order_creditmemo/view', array('creditmemo_id' => $creditmemoId));
    }
}
