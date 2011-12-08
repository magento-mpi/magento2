<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Target rule edit form
 *
 */

class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'Enterprise_TargetRule';
    protected $_controller = 'adminhtml_targetrule';

    public function __construct()
    {
        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Delete Rule'));
        $this->_addButton('save_and_continue_edit', array(
            'class' => 'save',
            'label' => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
        ), 3);

        $this->_formScripts[] = '
            function saveAndContinueEdit() {
                editForm.submit($(\'edit_form\').action + \'back/edit/\');
            }';
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_target_rule');
        if ($rule && $rule->getRuleId()) {
            return Mage::helper('Enterprise_TargetRule_Helper_Data')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('Enterprise_TargetRule_Helper_Data')->__('New Rule');
        }
    }

}
