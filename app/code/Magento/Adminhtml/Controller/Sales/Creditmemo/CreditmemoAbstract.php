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
namespace Magento\Adminhtml\Controller\Sales\Creditmemo;

class CreditmemoAbstract extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Adminhtml\Controller\Sales\Creditmemo
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_creditmemo')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Credit Memos'), __('Credit Memos'));
        return $this;
    }

    /**
     * Creditmemos grid
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Creditmemo'))
            ->renderLayout();
    }

    /**
     * Creditmemo information page
     */
    public function viewAction()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            $this->_forward('view', 'sales_order_creditmemo', null, array('come_from' => 'sales_creditmemo'));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId)) {
                $creditmemo->sendEmail();
                $historyItem = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Status\History\Collection')
                    ->getUnnotifiedForInstance($creditmemo, \Magento\Sales\Model\Order\Creditmemo::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }

                $this->_getSession()->addSuccess(__('We sent the message.'));
                $this->_redirect('*/sales_order_creditmemo/view', array(
                    'creditmemo_id' => $creditmemoId
                ));
            }
        }
    }

    public function pdfcreditmemosAction(){
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        if (!empty($creditmemosIds)) {
            $invoices = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Creditmemo\Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
                ->load();
            if (!isset($pdf)){
                $pdf = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($invoices);
            } else {
                $pages = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('creditmemo'
                    . \Mage::getSingleton('Magento\Core\Model\Date')->date('Y-m-d_H-i-s')
                    . '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    public function printAction()
    {
        /** @see \Magento\Adminhtml\Controller\Sales\Order\Invoice */
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId)) {
                $pdf = \Mage::getModel('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf(array($creditmemo));
                $this->_prepareDownloadResponse('creditmemo'
                        . \Mage::getSingleton('Magento\Core\Model\Date')->date('Y-m-d_H-i-s')
                        . '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }
}
