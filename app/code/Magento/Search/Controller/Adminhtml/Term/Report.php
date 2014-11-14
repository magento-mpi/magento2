<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Term;

class Report extends \Magento\Reports\Controller\Adminhtml\Index
{
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
    public function execute()
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
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reports::report_search');
    }
}
