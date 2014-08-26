<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

use \Magento\Framework\Model\Exception;
use Magento\Backend\App\Action;

class Cancel extends \Magento\Backend\App\Action
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
     * Cancel invoice action
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
            try {
                $invoice->cancel();
                $invoice->getOrder()->setIsInProcess(true);
                $this->_objectManager->create(
                    'Magento\Framework\DB\Transaction'
                )->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                )->save();
                $this->messageManager->addSuccess(__('You canceled the invoice.'));
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invoice canceling error'));
            }
            $this->_redirect('sales/*/view', array('invoice_id' => $invoice->getId()));
        } else {
            $this->_forward('noroute');
        }
    }
}
