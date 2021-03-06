<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Controller\Adminhtml\Report\Invitation;

class Order extends \Magento\Invitation\Controller\Adminhtml\Report\Invitation
{
    /**
     * Report by order action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_order'
        )->_addBreadcrumb(
            __('Invitation Report by Customers'),
            __('Invitation Report by Order Conversion Rate')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Conversion Rate Report'));
        $this->_view->renderLayout();
    }
}
