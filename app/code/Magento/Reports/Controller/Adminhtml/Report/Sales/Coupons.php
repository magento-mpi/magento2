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

class Coupons extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Coupons report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Coupons Report'));

        $this->_showLastExecutionTime(Flag::REPORT_COUPONS_FLAG_CODE, 'coupons');

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_salesroot_coupons'
        )->_addBreadcrumb(
            __('Coupons'),
            __('Coupons')
        );

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_coupons.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->_view->renderLayout();
    }
}
