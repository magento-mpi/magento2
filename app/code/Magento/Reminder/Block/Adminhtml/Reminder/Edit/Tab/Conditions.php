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
     * Fieldset block
     *
     * @var \Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_fieldsetBlock;

    /**
     * Conditions block
     *
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditionsBlock;

    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset $fieldsetBlock,
        \Magento\Rule\Block\Conditions $conditionsBlock,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_fieldsetBlock = $fieldsetBlock;
        $this->_conditionsBlock = $conditionsBlock;
    }

    /**
     * Prepare conditions form
     *
     * @return \Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Conditions
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
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
