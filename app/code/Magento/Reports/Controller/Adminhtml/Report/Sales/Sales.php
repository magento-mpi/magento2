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

class Sales extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Sales Report'));

        $this->_showLastExecutionTime(Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_salesroot_sales'
        )->_addBreadcrumb(
            __('Sales Report'),
            __('Sales Report')
        );

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_sales.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->_view->renderLayout();
    }
}
