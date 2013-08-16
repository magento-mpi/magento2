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
 * sales admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Report extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reports'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reports'));
        return $this;
    }


    public function searchAction()
    {
        $this->_title($this->__('Search Terms Report'));

        $this->_eventManager->dispatch('on_view_report', array('report' => 'search'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_search')
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')
            ->__('Search Terms'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Search Terms'))
            ->renderLayout();
    }

    /**
     * Export search report grid to CSV format
     */
    public function exportSearchCsvAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        $this->_prepareDownloadResponse('search.csv', $content->getCsvFile());
    }

    /**
     * Export search report to Excel XML format
     */
    public function exportSearchExcelAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        $this->_prepareDownloadResponse('search.xml', $content->getExcelFile());
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'search':
                return $this->_authorization->isAllowed('Mage_Reports::report_search');
                break;
            default:
                return $this->_authorization->isAllowed('Mage_Reports::report');
                break;
        }
    }
}
