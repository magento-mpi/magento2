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
 * sales admin controller
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Controller\Adminhtml;

use Magento\Framework\App\ResponseInterface;

class Index extends \Magento\Backend\App\Action
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
     * Add reports to breadcrumb
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        return $this;
    }

    /**
     * Search terms report action
     *
     * @return void
     */
    public function searchAction()
    {
        $this->_title->add(__('Search Terms Report'));

        $this->_eventManager->dispatch('on_view_report', array('report' => 'search'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_search'
        )->_addBreadcrumb(
            __('Search Terms'),
            __('Search Terms')
        );
        $this->_view->renderLayout();
    }

    /**
     * Export search report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportSearchCsvAction()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        return $this->_fileFactory->create('search.csv', $content->getCsvFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Export search report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportSearchExcelAction()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        return $this->_fileFactory->create('search.xml', $content->getExcelFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Determine if action is allowed for reports module
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'search':
                return $this->_authorization->isAllowed('Magento_Reports::report_search');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Reports::report');
                break;
        }
    }
}
