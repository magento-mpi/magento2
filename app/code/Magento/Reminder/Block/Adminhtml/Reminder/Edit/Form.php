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
 * Reminder rules edit form block
 */
class Magento_Reminder_Block_Adminhtml_Reminder_Edit_Form
    extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_reminder_rule_form');
        $this->setTitle(__('Email Reminder Rule'));
    }

    /**
     * Prepare edit form
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = $this->_createForm(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
