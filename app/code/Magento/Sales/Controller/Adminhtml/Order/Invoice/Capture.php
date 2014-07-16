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

class Capture extends \Magento\Backend\App\Action
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
     * Capture invoice action
     *
     * @return void
     */
    public function execute()
    {
        $invoice = $this->invoiceLoader->load($this->_request);
        if ($invoice) {
            try {
                $invoice->capture();
                $invoice->getOrder()->setIsInProcess(true);
                $this->_objectManager->create(
                    'Magento\Framework\DB\Transaction'
                )->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                )->save();
                $this->messageManager->addSuccess(__('The invoice has been captured.'));
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invoice capturing error'));
            }
            $this->_redirect('sales/*/view', array('invoice_id' => $invoice->getId()));
        } else {
            $this->_forward('noroute');
        }
    }
}
