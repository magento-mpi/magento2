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
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit;

class Form
    extends \Magento\Backend\Block\Widget\Form\Generic
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
     * @return \Magento\Reminder\Block\Adminhtml\Reminder\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
