<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Block\Adminhtml;

/**
 * Reminder Adminhtml Block
 */
class Reminder extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize reminders manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reminder';
        $this->_controller = 'adminhtml_reminder';
        $this->_headerText = __('Automated Email Marketing Reminder Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
