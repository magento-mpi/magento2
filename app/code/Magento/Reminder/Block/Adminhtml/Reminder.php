<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder Adminhtml Block
 */
class Magento_Reminder_Block_Adminhtml_Reminder extends Magento_Adminhtml_Block_Widget_Grid_Container
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
