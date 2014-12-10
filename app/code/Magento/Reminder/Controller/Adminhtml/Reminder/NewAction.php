<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Controller\Adminhtml\Reminder;

class NewAction extends \Magento\Reminder\Controller\Adminhtml\Reminder
{
    /**
     * Create new rule
     *
     * @return void
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
