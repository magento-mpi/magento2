<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit;

use Magento\Backend\Block\Widget\Form as WidgetForm;

/**
 * Reminder rules edit form block
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Initialize form
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
     * @return WidgetForm
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(
            array('data' => array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'))
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
