<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Controller\Adminhtml\Reminder;

class Index extends \Magento\Reminder\Controller\Adminhtml\Reminder
{
    /**
     * Rules list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Reminder::promo_reminder');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Email Reminders'));
        $this->_view->renderLayout();
    }
}
