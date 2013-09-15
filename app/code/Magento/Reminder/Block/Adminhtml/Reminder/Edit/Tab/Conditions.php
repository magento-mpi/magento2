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
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab;

class Conditions
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare conditions form
     *
     * @return \Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Conditions
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_reminder_rule');

        $renderer = \Mage::getBlockSingleton('Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/reminder/newConditionHtml/form/rule_conditions_fieldset'));
        $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
            'legend'  => __('Conditions'),
            'comment' => __('You need to set at least one condition for this rule to work.'),
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'required' => true,
        ))->setRule($model)->setRenderer(\Mage::getBlockSingleton('Magento\Rule\Block\Conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
