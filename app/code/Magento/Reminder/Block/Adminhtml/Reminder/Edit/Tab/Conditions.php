<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab;

use Magento\Backend\Block\Widget\Form;

/**
 * Reminder rules edit form conditions
 */
class Conditions extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Fieldset block
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_fieldsetBlock;

    /**
     * Conditions block
     *
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditionsBlock;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldsetBlock
     * @param \Magento\Rule\Block\Conditions $conditionsBlock
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldsetBlock,
        \Magento\Rule\Block\Conditions $conditionsBlock,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_fieldsetBlock = $fieldsetBlock;
        $this->_conditionsBlock = $conditionsBlock;
    }

    /**
     * Prepare conditions form
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_reminder_rule');

        $renderer = $this->_fieldsetBlock->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('adminhtml/reminder/newConditionHtml/form/rule_conditions_fieldset')
        );
        $fieldset = $form->addFieldset(
            'rule_conditions_fieldset',
            array(
                'legend' => __('Conditions'),
                'comment' => __('You need to set at least one condition for this rule to work.')
            )
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            array('name' => 'conditions', 'required' => true)
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditionsBlock
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
