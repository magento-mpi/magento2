<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_customers_totals'
        )->_addBreadcrumb(
            __('Customers by Orders Total'),
            __('Customers by Orders Total')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Order Total Report'));
        $this->_view->renderLayout();
    }
}
