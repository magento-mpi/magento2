<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Sales;

use \Magento\Reports\Model\Flag;

class Refunded extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Refunds report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Refunds Report'));

        $this->_showLastExecutionTime(Flag::REPORT_REFUNDED_FLAG_CODE, 'refunded');

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_salesroot_refunded'
        )->_addBreadcrumb(
            __('Total Refunded'),
            __('Total Refunded')
        );

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_refunded.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->_view->renderLayout();
    }
}
