<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder Adminhtml Block
 */
class Enterprise_Reminder_Block_Adminhtml_Reminder extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize reminders manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_Reminder';
        $this->_controller = 'adminhtml_reminder';
        $this->_headerText = __('Automated Email Marketing Reminder Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
