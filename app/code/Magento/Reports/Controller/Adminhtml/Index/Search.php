<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Index;

class Search extends \Magento\Reports\Controller\Adminhtml\Index
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
        $this->_eventManager->dispatch('on_view_report', array('report' => 'search'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_search'
        )->_addBreadcrumb(
            __('Search Terms'),
            __('Search Terms')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Search Terms Report'));
        $this->_view->renderLayout();
    }
}
