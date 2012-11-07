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
 *
 * Customer reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';

        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Reports'), Mage::helper('Mage_Reports_Helper_Data')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers'), Mage::helper('Mage_Reports_Helper_Data')->__('Customers'));
        return $this;
    }

    public function accountsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('New Accounts'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_customers_accounts')
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Accounts'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Accounts'))
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
        $exportBlock = $this->getLayout()->getChildBlock('report.customer.accounts.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export new accounts report grid to Excel XML format
     */
    public function exportAccountsExcelAction()
    {
        $this->loadLayout();
        $fileName = 'accounts.xml';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('report.customer.accounts.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    public function ordersAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers by Number of Orders'));

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
        $fileName   = 'customers_orders.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Orders_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customers most ordered report to Excel XML format
     */
    public function exportOrdersExcelAction()
    {
        $fileName   = 'customers_orders.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Orders_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function totalsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers by Orders Total'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_customers_totals')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Orders Total'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Orders Total'))
      //      ->_addCon($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Totals'))
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
        $exportBlock = $this->getLayout()->getChildBlock('report.customer.totals.grid', 'grid.export');
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
        $exportBlock = $this->getLayout()->getChildBlock('report.customer.totals.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'accounts':
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::accounts');
                break;
            case 'orders':
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::customers_orders');
                break;
            case 'totals':
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::totals');
                break;
            default:
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::customers');
                break;
        }
    }
}
