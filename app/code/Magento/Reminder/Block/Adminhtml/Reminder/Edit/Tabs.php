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
 * Reminder rules edit tabs block
 */
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit;

class Tabs
    extends \Magento\Adminhtml\Block\Widget\Tabs
{

    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_reminder_rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Email Reminder Rule'));
    }

    /**
     * Add tab sections
     *
     * @return \Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => __('Rule Information'),
            'content' => $this->getLayout()->createBlock(
                'Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\General',
                'adminhtml_reminder_edit_tab_general'
            )->toHtml(),
        ));

        $this->addTab('conditions_section', array(
            'label'   => __('Conditions'),
            'content' => $this->getLayout()->createBlock(
                'Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Conditions',
                'adminhtml_reminder_edit_tab_conditions'
            )->toHtml()
        ));

        $this->addTab('template_section', array(
            'label'   => __('Emails and Labels'),
            'content' => $this->getLayout()->createBlock(
                'Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Templates',
                'adminhtml_reminder_edit_tab_templates'
            )->toHtml()
        ));

        $rule = \Mage::registry('current_reminder_rule');
        if ($rule && $rule->getId()) {
            $this->addTab('matched_customers', array(
                'label' => __('Matched Customers'),
                'url'   => $this->getUrl('*/*/customerGrid', array('rule_id' => $rule->getId())),
                'class' => 'ajax'
            ));
        }

        return parent::_beforeToHtml();
    }
}
