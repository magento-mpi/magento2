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

class View extends \Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice\View
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
     * Invoice information page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Invoices'));
        $invoice = $this->invoiceLoader->load($this->_request);
        if ($invoice) {
            $this->_title->add(sprintf("#%s", $invoice->getIncrementId()));

            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_Sales::sales_order');
            $this->_view->getLayout()->getBlock(
                'sales_invoice_view'
            )->updateBackButtonUrl(
                $this->getRequest()->getParam('come_from')
            );
            $this->_view->renderLayout();
        } else {
            $this->_forward('noroute');
        }
    }
}
