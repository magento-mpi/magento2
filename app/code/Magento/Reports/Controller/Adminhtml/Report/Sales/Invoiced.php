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

class Invoiced extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Invoice report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Invoice Report'));

        $this->_showLastExecutionTime(Flag::REPORT_INVOICE_FLAG_CODE, 'invoiced');

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_salesroot_invoiced'
        )->_addBreadcrumb(
            __('Total Invoiced'),
            __('Total Invoiced')
        );

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_invoiced.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->_view->renderLayout();
    }
}
