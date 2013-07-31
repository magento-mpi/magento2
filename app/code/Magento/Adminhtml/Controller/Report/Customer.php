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
 *
 * Customer reports admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Report_Customer extends Magento_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if (!$act) {
            $act = 'default';
        }

        $this->loadLayout()
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Reports'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Reports')
            )
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers')
            );
        return $this;
    }

    public function accountsAction()
    {
        $this->_title($this->__('New Accounts Report'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_customers_accounts')
            ->_addBreadcrumb(
                Mage::helper('Magento_Adminhtml_Helper_Data')->__('New Accounts'),
                Mage::helper('Magento_Adminhtml_Helper_Data')->__('New Accounts')
            )
            ->renderLayout();
    }

    /**
     * Export new accounts report grid to CSV format
     */
    public function exportAccountsCsvAction()
    {
        $this->loadLayout();
        $fileName = 'new_accounts.csv';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export new accounts report grid to Excel XML format
     */
    public function exportAccountsExcelAction()
    {
        $this->loadLayout();
        $fileName = 'new_accounts.xml';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    public function ordersAction()
    {
        $this->_title($this->__('Order Count Report'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_customers_orders')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Number of Orders'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Number of Orders'))
            ->renderLayout();
    }

    /**
     * Export customers most ordered report to CSV format
     */
    public function exportOrdersCsvAction()
    {
        $this->loadLayout();
        $fileName = 'customers_orders.csv';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export customers most ordered report to Excel XML format
     */
    public function exportOrdersExcelAction()
    {
        $this->loadLayout();
        $fileName   = 'customers_orders.xml';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    public function totalsAction()
    {
        $this->_title($this->__('Order Total Report'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_customers_totals')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Orders Total'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Orders Total'))
            ->renderLayout();
    }

    /**
     * Export customers biggest totals report to CSV format
     */
    public function exportTotalsCsvAction()
    {
        $this->loadLayout();
        $fileName = 'customer_totals.csv';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export customers biggest totals report to Excel XML format
     */
    public function exportTotalsExcelAction()
    {
        $this->loadLayout();
        $fileName = 'customer_totals.xml';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'accounts':
                return $this->_authorization->isAllowed('Mage_Reports::accounts');
                break;
            case 'orders':
                return $this->_authorization->isAllowed('Mage_Reports::customers_orders');
                break;
            case 'totals':
                return $this->_authorization->isAllowed('Mage_Reports::totals');
                break;
            default:
                return $this->_authorization->isAllowed('Mage_Reports::customers');
                break;
        }
    }
}
