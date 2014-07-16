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

class Bestsellers extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Best sellers report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Best Sellers Report'));

        $this->_showLastExecutionTime(Flag::REPORT_BESTSELLERS_FLAG_CODE, 'bestsellers');

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_products_bestsellers'
        )->_addBreadcrumb(
            __('Products Bestsellers Report'),
            __('Products Bestsellers Report')
        );

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_bestsellers.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->_view->renderLayout();
    }
}
