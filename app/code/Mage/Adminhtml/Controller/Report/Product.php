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
 * Product reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Report_Product extends Mage_Adminhtml_Controller_Report_Abstract
{
    /**
     * Add report/products breadcrumbs
     *
     * @return Mage_Adminhtml_Controller_Report_Product
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(
            Mage::helper('Mage_Reports_Helper_Data')->__('Products'),
            Mage::helper('Mage_Reports_Helper_Data')->__('Products')
        );
        return $this;
    }

    /**
     * Sold Products Report Action
     *
     */
    public function soldAction()
    {
        $this->_title($this->__('Ordered Products Report'));
        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_products_sold')
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Products Ordered'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Products Ordered')
            )
            ->renderLayout();
    }

    /**
     * Export Sold Products report to CSV format action
     *
     */
    public function exportSoldCsvAction()
    {
        $this->loadLayout();
        $fileName   = 'products_ordered.csv';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export Sold Products report to XML format action
     *
     */
    public function exportSoldExcelAction()
    {
        $this->loadLayout();
        $fileName   = 'products_ordered.xml';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Most viewed products
     *
     */
    public function viewedAction()
    {
        $this->_title($this->__('Product Views Report'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE, 'viewed');

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_products_viewed')
            ->_addBreadcrumb(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('Products Most Viewed Report'),
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('Products Most Viewed Report')
            );

        $gridBlock = $this->getLayout()->getBlock('report_product_viewed.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export products most viewed report to CSV format
     *
     */
    public function exportViewedCsvAction()
    {
        $fileName   = 'products_mostviewed.csv';
        $grid       = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Viewed_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export products most viewed report to XML format
     *
     */
    public function exportViewedExcelAction()
    {
        $fileName   = 'products_mostviewed.xml';
        $grid       = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Viewed_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Low stock action
     *
     */
    public function lowstockAction()
    {
        $this->_title($this->__('Low Stock Report'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_products_lowstock')
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Low Stock'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Low Stock')
            );
            $this->renderLayout();
    }

    /**
     * Export low stock products report to CSV format
     *
     */
    public function exportLowstockCsvAction()
    {
        $this->loadLayout(false);
        $fileName = 'products_lowstock.csv';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.product.lowstock.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export low stock products report to XML format
     *
     */
    public function exportLowstockExcelAction()
    {
        $this->loadLayout(false);
        $fileName = 'products_lowstock.xml';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.product.lowstock.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile());
    }

    /**
     * Downloads action
     *
     */
    public function downloadsAction()
    {
        $this->_title($this->__('Downloads Report'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Downloadable::report_products_downloads')
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Downloads'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Downloads')
            )
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Downloads'))
            ->renderLayout();
    }

    /**
     * Export products downloads report to CSV format
     *
     */
    public function exportDownloadsCsvAction()
    {
        $fileName   = 'products_downloads.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Downloads_Grid')
            ->setSaveParametersInSession(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products downloads report to XLS format
     *
     */
    public function exportDownloadsExcelAction()
    {
        $fileName   = 'products_downloads.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Downloads_Grid')
            ->setSaveParametersInSession(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'viewed':
                return $this->_authorization->isAllowed('Mage_Reports::viewed');
                break;
            case 'sold':
                return $this->_authorization->isAllowed('Mage_Reports::sold');
                break;
            case 'lowstock':
                return $this->_authorization->isAllowed('Mage_Reports::lowstock');
                break;
            default:
                return $this->_authorization->isAllowed('Mage_Reports::report_products');
                break;
        }
    }
}
