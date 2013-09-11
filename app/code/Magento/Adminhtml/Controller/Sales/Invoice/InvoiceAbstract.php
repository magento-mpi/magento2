<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Sales\Invoice;

class InvoiceAbstract
    extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Adminhtml\Controller\Sales\Invoice
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_invoice')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Invoices'), __('Invoices'));
        return $this;
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Invoice\Grid')->toHtml()
        );
    }

    /**
     * Invoices grid
     */
    public function indexAction()
    {
        $this->_title(__('Invoices'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Invoice'))
            ->renderLayout();
    }

    /**
     * Invoice information page
     */
    public function viewAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $this->_forward('view', 'sales_order_invoice', null, array('come_from'=>'invoice'));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = \Mage::getModel('Magento\Sales\Model\Order\Invoice')->load($invoiceId)) {
                $invoice->sendEmail();
                $historyItem = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Status\History\Collection')
                    ->getUnnotifiedForInstance($invoice, \Magento\Sales\Model\Order\Invoice::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess(__('We sent the message.'));
                $this->_redirect('*/sales_invoice/view', array(
                    'order_id'  => $invoice->getOrder()->getId(),
                    'invoice_id'=> $invoiceId,
                ));
            }
        }
    }

    public function printAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = \Mage::getModel('Magento\Sales\Model\Order\Invoice')->load($invoiceId)) {
                $pdf = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf(array($invoice));
                $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('Magento\Core\Model\Date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function pdfinvoicesAction(){
        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        if (!empty($invoicesIds)) {
            $invoices = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Invoice\Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
                ->load();
            if (!isset($pdf)){
                $pdf = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
            } else {
                $pages = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('Magento\Core\Model\Date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_invoice');
    }

}
