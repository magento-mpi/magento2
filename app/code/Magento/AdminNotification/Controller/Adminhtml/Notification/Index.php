<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Controller\Adminhtml\Notification;

class Index extends \Magento\AdminNotification\Controller\Adminhtml\Notification
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Notifications'));

        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_AdminNotification::system_adminnotification'
        )->_addBreadcrumb(
            __('Messages Inbox'),
            __('Messages Inbox')
        );
        $this->_view->renderLayout();
    }
}
