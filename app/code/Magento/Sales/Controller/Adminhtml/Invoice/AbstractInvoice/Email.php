<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice;

use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

abstract class Email extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_invoice');
    }

    /**
     * Notify user
     *
     * @return void
     */
    public function execute()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if (!$invoiceId) {
            return;
        }
        $invoice = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
        if (!$invoice) {
            return;
        }

        $this->_objectManager->create('Magento\Sales\Model\OrderNotifier')
            ->notify($invoice);

        $this->messageManager->addSuccess(__('We sent the message.'));
        $this->_redirect(
            'sales/invoice/view',
            array('order_id' => $invoice->getOrder()->getId(), 'invoice_id' => $invoiceId)
        );
    }
}
