<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Statistics;

class Index extends \Magento\Reports\Controller\Adminhtml\Report\Statistics
{
    /**
     * Refresh statistics action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Refresh Statistics'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_statistics_refresh'
        )->_addBreadcrumb(
            __('Refresh Statistics'),
            __('Refresh Statistics')
        );
        $this->_view->renderLayout();
    }
}
