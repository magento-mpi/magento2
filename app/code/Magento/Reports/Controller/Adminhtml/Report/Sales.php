<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales report admin controller
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Controller\Adminhtml\Report;

class Sales extends \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return \Magento\Reports\Controller\Adminhtml\Report\Sales
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(__('Sales'), __('Sales'));
        return $this;
    }

    public function salesAction()
    {
        $this->_title->add(__('Sales Report'));

        $this->_showLastExecutionTime(\Magento\Reports\Model\Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_sales')
            ->_addBreadcrumb(__('Sales Report'), __('Sales Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_sales.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_view->renderLayout();
    }

    public function bestsellersAction()
    {
        $this->_title->add(__('Best Sellers Report'));

        $this->_showLastExecutionTime(\Magento\Reports\Model\Flag::REPORT_BESTSELLERS_FLAG_CODE, 'bestsellers');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_products_bestsellers')
            ->_addBreadcrumb(__('Products Bestsellers Report'), __('Products Bestsellers Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_bestsellers.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_view->renderLayout();
    }

    /**
     * Export bestsellers report grid to CSV format
     */
    public function exportBestsellersCsvAction()
    {
        $fileName   = 'bestsellers.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Bestsellers\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export bestsellers report grid to Excel XML format
     */
    public function exportBestsellersExcelAction()
    {
        $fileName   = 'bestsellers.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Bestsellers\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Refresh statistics for last 25 hours
     *
     * @return \Magento\Reports\Controller\Adminhtml\Report\Sales
     */
    public function refreshRecentAction()
    {
        $this->_forward('refreshRecent', 'report_statistics');
    }

    /**
     * Refresh statistics for all period
     *
     * @return \Magento\Reports\Controller\Adminhtml\Report\Sales
     */
    public function refreshLifetimeAction()
    {
        $this->_forward('refreshLifetime', 'report_statistics');
    }

    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'sales.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Sales\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'sales.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Sales\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    public function taxAction()
    {
        $this->_title->add(__('Tax Report'));

        $this->_showLastExecutionTime(\Magento\Reports\Model\Flag::REPORT_TAX_FLAG_CODE, 'tax');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_tax')
            ->_addBreadcrumb(__('Tax'), __('Tax'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_tax.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_view->renderLayout();
    }

    /**
     * Export tax report grid to CSV format
     */
    public function exportTaxCsvAction()
    {
        $fileName   = 'tax.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Tax\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export tax report grid to Excel XML format
     */
    public function exportTaxExcelAction()
    {
        $fileName   = 'tax.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Tax\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    public function shippingAction()
    {
        $this->_title->add(__('Shipping Report'));

        $this->_showLastExecutionTime(\Magento\Reports\Model\Flag::REPORT_SHIPPING_FLAG_CODE, 'shipping');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_shipping')
            ->_addBreadcrumb(__('Shipping'), __('Shipping'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_shipping.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_view->renderLayout();
    }

    /**
     * Export shipping report grid to CSV format
     */
    public function exportShippingCsvAction()
    {
        $fileName   = 'shipping.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Shipping\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export shipping report grid to Excel XML format
     */
    public function exportShippingExcelAction()
    {
        $fileName   = 'shipping.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Shipping\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    public function invoicedAction()
    {
        $this->_title->add(__('Invoice Report'));

        $this->_showLastExecutionTime(\Magento\Reports\Model\Flag::REPORT_INVOICE_FLAG_CODE, 'invoiced');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_invoiced')
            ->_addBreadcrumb(__('Total Invoiced'), __('Total Invoiced'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_invoiced.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_view->renderLayout();
    }

    /**
     * Export invoiced report grid to CSV format
     */
    public function exportInvoicedCsvAction()
    {
        $fileName   = 'invoiced.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Invoiced\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export invoiced report grid to Excel XML format
     */
    public function exportInvoicedExcelAction()
    {
        $fileName   = 'invoiced.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Invoiced\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    public function refundedAction()
    {
        $this->_title->add(__('Refunds Report'));

        $this->_showLastExecutionTime(\Magento\Reports\Model\Flag::REPORT_REFUNDED_FLAG_CODE, 'refunded');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_refunded')
            ->_addBreadcrumb(__('Total Refunded'), __('Total Refunded'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_refunded.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_view->renderLayout();
    }

    /**
     * Export refunded report grid to CSV format
     */
    public function exportRefundedCsvAction()
    {
        $fileName   = 'refunded.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Refunded\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export refunded report grid to Excel XML format
     */
    public function exportRefundedExcelAction()
    {
        $fileName   = 'refunded.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Refunded\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    public function couponsAction()
    {
        $this->_title->add(__('Coupons Report'));

        $this->_showLastExecutionTime(\Magento\Reports\Model\Flag::REPORT_COUPONS_FLAG_CODE, 'coupons');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_coupons')
            ->_addBreadcrumb(__('Coupons'), __('Coupons'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_coupons.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->_view->renderLayout();
    }

    /**
     * Export coupons report grid to CSV format
     */
    public function exportCouponsCsvAction()
    {
        $fileName   = 'coupons.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Coupons\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     * Export coupons report grid to Excel XML format
     */
    public function exportCouponsExcelAction()
    {
        $fileName   = 'coupons.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Sales\Coupons\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    public function refreshStatisticsAction()
    {
        $this->_forward('index', 'report_statistics');
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'sales':
                return $this->_authorization->isAllowed('Magento_Reports::salesroot_sales');
                break;
            case 'tax':
                return $this->_authorization->isAllowed('Magento_Reports::tax');
                break;
            case 'shipping':
                return $this->_authorization->isAllowed('Magento_Reports::shipping');
                break;
            case 'invoiced':
                return $this->_authorization->isAllowed('Magento_Reports::invoiced');
                break;
            case 'refunded':
                return $this->_authorization->isAllowed('Magento_Reports::refunded');
                break;
            case 'coupons':
                return $this->_authorization->isAllowed('Magento_Reports::coupons');
                break;
            case 'shipping':
                return $this->_authorization->isAllowed('Magento_Reports::shipping');
                break;
            case 'bestsellers':
                return $this->_authorization->isAllowed('Magento_Reports::bestsellers');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Reports::salesroot');
                break;
        }
    }
}
