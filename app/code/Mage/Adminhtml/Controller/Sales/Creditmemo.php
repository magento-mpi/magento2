<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Sales_Creditmemo extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_CreditmemoController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Mage_Sales::sales_creditmemo')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Credit Memos'),$this->__('Credit Memos'));
        return $this;
    }

    /**
     * Creditmemos grid
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Sales_Creditmemo'))
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
            if ($creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo')->load($creditmemoId)) {
                $creditmemo->sendEmail();
                $historyItem = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Status_History_Collection')
                    ->getUnnotifiedForInstance($creditmemo, Mage_Sales_Model_Order_Creditmemo::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }

                $this->_getSession()->addSuccess(Mage::helper('Mage_Sales_Helper_Data')->__('We sent the message.'));
                $this->_redirect('*/sales_order_creditmemo/view', array(
                    'creditmemo_id' => $creditmemoId
                ));
            }
        }
    }

    public function pdfcreditmemosAction(){
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        if (!empty($creditmemosIds)) {
            $invoices = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Creditmemo_Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('Mage_Sales_Model_Order_Pdf_Creditmemo')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('Mage_Sales_Model_Order_Pdf_Creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('Mage_Core_Model_Date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    public function printAction()
    {
        /** @see Mage_Adminhtml_Sales_Order_InvoiceController */
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo')->load($creditmemoId)) {
                $pdf = Mage::getModel('Mage_Sales_Model_Order_Pdf_Creditmemo')->getPdf(array($creditmemo));
                $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('Mage_Core_Model_Date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Sales::sales_creditmemo');
    }
}
