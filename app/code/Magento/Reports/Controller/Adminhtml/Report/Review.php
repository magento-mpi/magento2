<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review reports admin controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Controller\Adminhtml\Report;

use Magento\Framework\App\ResponseInterface;

class Review extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Add reports and reviews breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        $this->_addBreadcrumb(__('Review'), __('Reviews'));
        return $this;
    }

    /**
     * Customer Reviews Report action
     *
     * @return void
     */
    public function customerAction()
    {
        $this->_title->add(__('Customer Reviews Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Review::report_review_customer'
        )->_addBreadcrumb(
            __('Customers Report'),
            __('Customers Report')
        );
        $this->_view->renderLayout();
    }

    /**
     * Export review customer report to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCustomerCsvAction()
    {
        $this->_view->loadLayout(false);
        $fileName = 'review_customer.csv';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.review.customer.grid',
            'grid.export'
        );
        return $this->_fileFactory->create($fileName, $exportBlock->getCsvFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Export review customer report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportCustomerExcelAction()
    {
        $this->_view->loadLayout(false);
        $fileName = 'review_customer.xml';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.review.customer.grid',
            'grid.export'
        );
        return $this->_fileFactory->create($fileName, $exportBlock->getExcelFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Product reviews report action
     *
     * @return void
     */
    public function productAction()
    {
        $this->_title->add(__('Product Reviews Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Review::report_review_product'
        )->_addBreadcrumb(
            __('Products Report'),
            __('Products Report')
        );
        $this->_view->renderLayout();
    }

    /**
     * Export review product report to CSV format
     *
     * @return ResponseInterface
     */
    public function exportProductCsvAction()
    {
        $this->_view->loadLayout(false);
        $fileName = 'review_product.csv';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.review.product.grid',
            'grid.export'
        );
        return $this->_fileFactory->create($fileName, $exportBlock->getCsvFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Export review product report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportProductExcelAction()
    {
        $this->_view->loadLayout(false);
        $fileName = 'review_product.xml';
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.review.product.grid',
            'grid.export'
        );
        return $this->_fileFactory->create($fileName, $exportBlock->getExcelFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Details action
     *
     * @return void
     */
    public function productDetailAction()
    {
        $this->_title->add(__('Details'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Review::report_review'
        )->_addBreadcrumb(
            __('Products Report'),
            __('Products Report')
        )->_addBreadcrumb(
            __('Product Reviews'),
            __('Product Reviews')
        )->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Review\Detail')
        );
        $this->_view->renderLayout();
    }

    /**
     * Export review product detail report to CSV format
     *
     * @return ResponseInterface
     */
    public function exportProductDetailCsvAction()
    {
        $fileName = 'review_product_detail.csv';
        $content = $this->_view->getLayout()->createBlock(
            'Magento\Reports\Block\Adminhtml\Review\Detail\Grid'
        )->getCsv();

        return $this->_fileFactory->create($fileName, $content, \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Export review product detail report to ExcelXML format
     *
     * @return ResponseInterface
     */
    public function exportProductDetailExcelAction()
    {
        $fileName = 'review_product_detail.xml';
        $content = $this->_view->getLayout()->createBlock(
            'Magento\Reports\Block\Adminhtml\Review\Detail\Grid'
        )->getExcel(
            $fileName
        );

        return $this->_fileFactory->create($fileName, $content, \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Determine if action is allowed for reports module
     *
     * @return bool
     */
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
