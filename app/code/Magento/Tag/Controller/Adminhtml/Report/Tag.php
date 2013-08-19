<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag report admin controller
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Controller_Adminhtml_Report_Tag extends Magento_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(
                __('Reports'),
                __('Reports')
            )
            ->_addBreadcrumb(
                __('Tag'),
                __('Tag')
            );

        return $this;
    }

    public function customerAction()
    {
        $this->_title(__('Customer Report'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Tag::report_tags_customer')
            ->_addBreadcrumb(
                __('Customers Report'),
                __('Customers Report'))
            ->renderLayout();
    }

    /**
     * Export customer's tags report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $this->loadLayout(false);
        $content    = $this->getLayout()->getChildBlock('adminhtml.report.tag.customer.grid','grid.export');
        $this->_prepareDownloadResponse('tag_customer.csv', $content->getCsvFile());
    }

    /**
     * Export customer's tags report to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $this->loadLayout(false);
        $content    = $this->getLayout()->getChildBlock('adminhtml.report.tag.customer.grid','grid.export');
        $this->_prepareDownloadResponse('tag_customer.xml', $content->getExcelFile());
    }

    public function productAction()
    {
        $this->_title(__('Product Report'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Tag::report_tags_product')
            ->_addBreadcrumb(
                __('Poducts Report'),
                __('Products Report')
            )
            ->renderLayout();
    }

    /**
     * Export product's tags report to CSV format
     */
    public function exportProductCsvAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.product.grid','grid.export');
        $this->_prepareDownloadResponse('tag_product.csv', $content->getCsvFile());
    }

    /**
     * Export product's tags report to Excel XML format
     */
    public function exportProductExcelAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.product.grid','grid.export');
        $this->_prepareDownloadResponse('tag_product.xml', $content->getExcelFile());
    }

    public function popularAction()
    {
        $this->_title(__('Reports'))
             ->_title(__('Tags'))
             ->_title(__('Popularity Report'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Tag::report_tags_popular')
            ->_addBreadcrumb(
                __('Popular Tags'),
                __('Popular Tags')
            )
            ->_addContent($this->getLayout()->createBlock('Magento_Tag_Block_Adminhtml_Report_Popular'))
            ->renderLayout();
    }

    /**
     * Export popular tags report to CSV format
     */
    public function exportPopularCsvAction()
    {
        $fileName   = 'tag_popular.csv';
        $content    = $this->getLayout()->createBlock('Magento_Tag_Block_Adminhtml_Report_Popular_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export popular tags report to Excel XML format
     */
    public function exportPopularExcelAction()
    {
        $fileName   = 'tag_popular.xml';
        $content    = $this->getLayout()->createBlock('Magento_Tag_Block_Adminhtml_Report_Popular_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function customerDetailAction()
    {
        $this->_initAction();

        /** @var $detailBlock Magento_Tag_Block_Adminhtml_Report_Customer_Detail */
        $detailBlock = $this->getLayout()->getBlock('report.tag.customer.detail.container');

        $this->_title(__('Reports'))->_title(__('Tags'))->_title(__('Customers'))
            ->_title($detailBlock->getHeaderText());

        $this->_setActiveMenu('Magento_Tag::report_tags')->_addBreadcrumb(__('Customers Report'), __('Customers Report'))
            ->_addBreadcrumb(__('Customer Tags'),
            __('Customer Tags'))->renderLayout();
    }

    /**
     * Export customer's tags detail report to CSV format
     */
    public function exportCustomerDetailCsvAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.customer.detail.grid','grid.export');
        $this->_prepareDownloadResponse('tag_customer_detail.csv', $content->getCsvFile());
    }

    /**
     * Export customer's tags detail report to Excel XML format
     */
    public function exportCustomerDetailExcelAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.customer.detail.grid', 'grid.export');
        $this->_prepareDownloadResponse('tag_customer_detail.xml', $content->getExcelFile());
    }

    public function productDetailAction()
    {
        $this->_initAction();

        /** @var $detailBlock Magento_Tag_Block_Adminhtml_Report_Product_Detail */
        $detailBlock = $this->getLayout()->getBlock('report.tag.product.productdetail.container');

        $this->_title(__('Reports'))
            ->_title(__('Tags'))
            ->_title(__('Products'))
            ->_title($detailBlock->getHeaderText());

        $this->_setActiveMenu('Magento_Tag::report_tags')
            ->_addBreadcrumb(
                __('Products Report'),
                __('Products Report')
            )
            ->_addBreadcrumb(
                __('Product Tags'),
                __('Product Tags')
            )->renderLayout();
    }

    /**
     * Export product's tags detail report to CSV format
     */
    public function exportProductDetailCsvAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.product.productdetail.grid','grid.export');
        $this->_prepareDownloadResponse('tag_product_detail.csv', $content->getCsvFile());
    }

    /**
     * Export product's tags detail report to Excel XML format
     */
    public function exportProductDetailExcelAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.product.productdetail.grid','grid.export');
        $this->_prepareDownloadResponse('tag_product_detail.xml', $content->getExcelFile());
    }

    public function tagDetailAction()
    {
        $this->_initAction();

        /** @var $detailBlock Magento_Tag_Block_Adminhtml_Report_Popular_Detail */
        $detailBlock = $this->getLayout()->getBlock('report.tag.detail.container');

        $this->_title(__('Reports'))
             ->_title(__('Tags'))
             ->_title(__('Popular'))
             ->_title($detailBlock->getHeaderText());

        $this->_setActiveMenu('Magento_Tag::report_tags')
            ->_addBreadcrumb(
                __('Popular Tags'),
                __('Popular Tags')
            )
            ->_addBreadcrumb(
                __('Tag Detail'),
                __('Tag Detail'))
            ->renderLayout();
    }

    /**
     * Export tag detail report to CSV format
     */
    public function exportTagDetailCsvAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.detail.grid','grid.export');
        $this->_prepareDownloadResponse('tag_detail.csv', $content->getCsvFile());
    }

    /**
     * Export tag detail report to Excel XML format
     */
    public function exportTagDetailExcelAction()
    {
        $this->loadLayout(false);
        $content = $this->getLayout()->getChildBlock('adminhtml.report.tag.detail.grid','grid.export');
        $this->_prepareDownloadResponse('tag_detail.xml', $content->getExcelFile());
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return $this->_authorization->isAllowed('Magento_Reports::tags_customer');
                break;
            case 'product':
                return $this->_authorization->isAllowed('Magento_Reports::tags_product');
                break;
            case 'productAll':
                return $this->_authorization->isAllowed('Magento_Reports::tags_product');
                break;
            case 'popular':
                return $this->_authorization->isAllowed('Magento_Reports::popular');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Reports::tags');
                break;
        }
    }
}
