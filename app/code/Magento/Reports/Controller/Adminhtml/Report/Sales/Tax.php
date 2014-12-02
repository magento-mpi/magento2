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

class Tax extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Tax report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_showLastExecutionTime(Flag::REPORT_TAX_FLAG_CODE, 'tax');

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_salesroot_tax'
        )->_addBreadcrumb(
            __('Tax'),
            __('Tax')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Tax Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_tax.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->_view->renderLayout();
    }
}
