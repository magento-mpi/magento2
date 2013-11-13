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
 * sales admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller;

class Report extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function _initAction()
    {
        $this->_layoutServices->loadLayout()
            ->_addBreadcrumb(__('Reports'), __('Reports'));
        return $this;
    }


    public function searchAction()
    {
        $this->_title->add(__('Search Terms Report'));

        $this->_eventManager->dispatch('on_view_report', array('report' => 'search'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_search')
            ->_addBreadcrumb(__('Search Terms'), __('Search Terms'))
            ->renderLayout();
    }

    /**
     * Export search report grid to CSV format
     */
    public function exportSearchCsvAction()
    {
        $this->_layoutServices->loadLayout(false);
        $content = $this->_layoutServices->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        return $this->_fileFactory->create('search.csv', $content->getCsvFile());
    }

    /**
     * Export search report to Excel XML format
     */
    public function exportSearchExcelAction()
    {
        $this->_layoutServices->loadLayout(false);
        $content = $this->_layoutServices->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        return $this->_fileFactory->create('search.xml', $content->getExcelFile());
    }

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
