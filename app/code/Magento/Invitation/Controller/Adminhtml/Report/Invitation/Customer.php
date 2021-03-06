<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_customer'
        )->_addBreadcrumb(
            __('Invitation Report by Customers'),
            __('Invitation Report by Customers')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Invited Customers Report'));
        $this->_view->renderLayout();
    }
}
