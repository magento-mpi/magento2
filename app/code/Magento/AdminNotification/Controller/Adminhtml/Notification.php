<?php
/**
 * Adminhtml AdminNotification controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Controller\Adminhtml;

class Notification extends \Magento\Backend\App\AbstractAction
{

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'markAsRead':
                $acl = 'Magento_AdminNotification::mark_as_read';
                break;

            case 'massMarkAsRead':
                $acl = 'Magento_AdminNotification::mark_as_read';
                break;

            case 'remove':
                $acl = 'Magento_AdminNotification::adminnotification_remove';
                break;

            case 'massRemove':
                $acl = 'Magento_AdminNotification::adminnotification_remove';
                break;

            default:
                $acl = 'Magento_AdminNotification::show_list';
        }
        return $this->_authorization->isAllowed($acl);
    }
}
