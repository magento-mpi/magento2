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
 * Reminder rules edit form conditions
 */
class Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Conditions
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare conditions form
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Conditions
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();
        $model = Mage::registry('current_reminder_rule');

        $renderer = Mage::getBlockSingleton('Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/reminder/newConditionHtml/form/rule_conditions_fieldset'));
        $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
            'legend'  => __('Conditions'),
            'comment' => __('You need to set at least one condition for this rule to work.'),
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('Magento_Rule_Block_Conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
