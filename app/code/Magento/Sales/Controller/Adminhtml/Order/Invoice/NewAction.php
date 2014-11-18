<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

use Magento\Backend\App\Action;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader
     */
    protected $invoiceLoader;

    /**
     * @param Action\Context $context
     * @param \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader $invoiceLoader
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader $invoiceLoader
    ) {
        $this->invoiceLoader = $invoiceLoader;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_invoice');
    }

    /**
     * Invoice create page
     *
     * @return void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $invoiceData = $this->getRequest()->getParam('invoice', []);
        $invoiceData = isset($invoiceData['items']) ? $invoiceData['items'] : [];
        $invoice = $this->invoiceLoader->load($orderId, $invoiceId, $invoiceData);
        if ($invoice) {
            $comment = $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
            if ($comment) {
                $invoice->setCommentText($comment);
            }

            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_Sales::sales_order');
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Invoices'));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Invoice'));
            $this->_view->renderLayout();
        } else {
            $this->_redirect('sales/order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
        }
    }
}
