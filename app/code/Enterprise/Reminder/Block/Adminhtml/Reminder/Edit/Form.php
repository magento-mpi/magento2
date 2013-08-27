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
 * Reminder rules edit form block
 */
class Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Form
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
        $this->setId('enterprise_reminder_rule_form');
        $this->setTitle(__('Email Reminder Rule'));
    }

    /**
     * Prepare edit form
     *
     * @return Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
