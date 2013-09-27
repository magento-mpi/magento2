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
    extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Fieldset block
     *
     * @var Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset
     */
    protected $_fieldsetBlock;

    /**
     * Conditions block
     *
     * @var Magento_Rule_Block_Conditions
     */
    protected $_conditionsBlock;

    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset $fieldsetBlock,
        Magento_Rule_Block_Conditions $conditionsBlock,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_fieldsetBlock = $fieldsetBlock;
        $this->_conditionsBlock = $conditionsBlock;
    }

    /**
     * Prepare conditions form
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Conditions
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_reminder_rule');

        $renderer = $this->_fieldsetBlock
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/reminder/newConditionHtml/form/rule_conditions_fieldset'));
        $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
            'legend'  => __('Conditions'),
            'comment' => __('You need to set at least one condition for this rule to work.'),
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'required' => true,
        ))->setRule($model)->setRenderer($this->_conditionsBlock);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
