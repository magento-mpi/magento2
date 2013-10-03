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
 * Review reports admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Report;

class Review extends \Magento\Adminhtml\Controller\Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(
                __('Reports'),
                __('Reports')
            )
            ->_addBreadcrumb(
                __('Review'),
                __('Reviews')
            );
        return $this;
    }

    public function customerAction()
    {
        $this->_title(__('Customer Reviews Report'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Review::report_review_customer')
            ->_addBreadcrumb(
                __('Customers Report'),
                __('Customers Report')
            );
         $this->renderLayout();
    }

    /**
     * Export review customer report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $this->loadLayout(false);
        $fileName = 'review_customer.csv';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.review.customer.grid','grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export review customer report to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $this->loadLayout(false);
        $fileName = 'review_customer.xml';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.review.customer.grid','grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile());

    }

    public function productAction()
    {
        $this->_title(__('Product Reviews Report'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Review::report_review_product')
            ->_addBreadcrumb(
            __('Products Report'),
            __('Products Report')
        );
            $this->renderLayout();
    }

    /**
     * Export review product report to CSV format
     */
    public function exportProductCsvAction()
    {
        $this->loadLayout(false);
        $fileName = 'review_product.csv';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.review.product.grid','grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export review product report to Excel XML format
     */
    public function exportProductExcelAction()
    {
        $this->loadLayout(false);
        $fileName = 'review_product.xml';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.review.product.grid','grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile());
    }

    public function productDetailAction()
    {
        $this->_title(__('Details'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Review::report_review')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addBreadcrumb(__('Product Reviews'), __('Product Reviews'))
            ->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Report\Review\Detail'))
            ->renderLayout();
    }

    /**
     * Export review product detail report to CSV format
     */
    public function exportProductDetailCsvAction()
    {
        $fileName   = 'review_product_detail.csv';
        $content    = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Report\Review\Detail\Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export review product detail report to ExcelXML format
     */
    public function exportProductDetailExcelAction()
    {
        $fileName   = 'review_product_detail.xml';
        $content    = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Report\Review\Detail\Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return $this->_authorization->isAllowed('Magento_Reports::review_customer');
                break;
            case 'product':
                return $this->_authorization->isAllowed('Magento_Reports::review_product');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Reports::review');
                break;
        }
    }
}
