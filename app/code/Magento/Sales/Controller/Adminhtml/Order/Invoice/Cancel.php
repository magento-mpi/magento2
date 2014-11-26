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

class Cancel extends \Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice\View
{
    /**
     * Cancel invoice action
     *
     * @return void
     */
    public function execute()
    {
        $invoice = $this->getInvoice();
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
    }
}
