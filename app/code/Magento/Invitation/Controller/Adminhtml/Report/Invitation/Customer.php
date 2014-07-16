<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Report\Invitation;

class Customer extends \Magento\Invitation\Controller\Adminhtml\Report\Invitation
{
    /**
     * Report by customers action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Invited Customers Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_customer'
        )->_addBreadcrumb(
            __('Invitation Report by Customers'),
            __('Invitation Report by Customers')
        );
        $this->_view->renderLayout();
    }
}
