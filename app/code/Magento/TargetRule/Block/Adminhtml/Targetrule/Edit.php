<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Target rule edit form block
 */

class Magento_TargetRule_Block_Adminhtml_Targetrule_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'Magento_TargetRule';
    protected $_controller = 'adminhtml_targetrule';

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => __('Save and Continue Edit'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 3);
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = Mage::registry('current_target_rule');
        if ($rule && $rule->getRuleId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($rule->getName()));
        }
        else {
            return __('New Rule');
        }
    }

}
