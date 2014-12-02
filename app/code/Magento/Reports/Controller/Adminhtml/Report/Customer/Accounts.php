<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Customer;

class Accounts extends \Magento\Reports\Controller\Adminhtml\Report\Customer
{
    /**
     * New accounts action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_customers_accounts'
        )->_addBreadcrumb(
            __('New Accounts'),
            __('New Accounts')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Accounts Report'));
        $this->_view->renderLayout();
    }
}
