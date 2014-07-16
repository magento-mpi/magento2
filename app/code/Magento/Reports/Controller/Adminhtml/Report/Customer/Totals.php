<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Customer;

class Totals extends \Magento\Reports\Controller\Adminhtml\Report\Customer
{
    /**
     * Customers by orders total action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Order Total Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_customers_totals'
        )->_addBreadcrumb(
            __('Customers by Orders Total'),
            __('Customers by Orders Total')
        );
        $this->_view->renderLayout();
    }
}
