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
class Mage_Adminhtml_Report_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init
     *
     * @return Mage_Adminhtml_Report_ProductController
     */
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Reports'), Mage::helper('Mage_Reports_Helper_Data')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Products'), Mage::helper('Mage_Reports_Helper_Data')->__('Products'));
        return $this;
    }

    /**
     * Sold Products Report Action
     *
     */
    public function soldAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Products Ordered'));

        $this->_initAction()
            ->_setActiveMenu('report/product/sold')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Products Ordered'), Mage::helper('Mage_Reports_Helper_Data')->__('Products Ordered'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Sold'))
            ->renderLayout();
    }

    /**
     * Export Sold Products report to CSV format action
     *
     */
    public function exportSoldCsvAction()
    {
        $fileName   = 'products_ordered.csv';
        $content    = $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Report_Product_Sold_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Sold Products report to XML format action
     *
     */
    public function exportSoldExcelAction()
    {
        $fileName   = 'products_ordered.xml';
        $content    = $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Report_Product_Sold_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Most viewed products
     *
     */
    public function viewedAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Most Viewed'));

        $this->_initAction()
            ->_setActiveMenu('report/product/viewed')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Most Viewed'), Mage::helper('Mage_Reports_Helper_Data')->__('Most Viewed'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Viewed'))
            ->renderLayout();
    }

    /**
     * Export products most viewed report to CSV format
     *
     */
    public function exportViewedCsvAction()
    {
        $fileName   = 'products_mostviewed.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Viewed_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products most viewed report to XML format
     *
     */
    public function exportViewedExcelAction()
    {
        $fileName   = 'products_mostviewed.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Viewed_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Low stock action
     *
     */
    public function lowstockAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Low Stock'));

        $this->_initAction()
            ->_setActiveMenu('report/product/lowstock')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Low Stock'), Mage::helper('Mage_Reports_Helper_Data')->__('Low Stock'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Lowstock'))
            ->renderLayout();
    }

    /**
     * Export low stock products report to CSV format
     *
     */
    public function exportLowstockCsvAction()
    {
        $fileName   = 'products_lowstock.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Lowstock_Grid')
            ->setSaveParametersInSession(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export low stock products report to XML format
     *
     */
    public function exportLowstockExcelAction()
    {
        $fileName   = 'products_lowstock.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Product_Lowstock_Grid')
            ->setSaveParametersInSession(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Downloads action
     *
     */
    public function downloadsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Downloads'));

        $this->_initAction()
            ->_setActiveMenu('report/product/downloads')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Downloads'), Mage::helper('Mage_Reports_Helper_Data')->__('Downloads'))
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
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/products/viewed');
                break;
            case 'sold':
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/products/sold');
                break;
            case 'lowstock':
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/products/lowstock');
                break;
            default:
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/products');
                break;
        }
    }
}
