<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab;

/**
 * Main target rules properties edit form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_rule;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\TargetRule\Model\Rule $rule
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\TargetRule\Model\Rule $rule,
        array $data = []
    ) {
        $this->_rule = $rule;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Mail Target Rule Edit form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\TargetRule\Model\Rule */
        $model = $this->_coreRegistry->registry('current_target_rule');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Rule Information')]);

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField('name', 'text', ['name' => 'name', 'label' => __('Rule Name'), 'required' => true]);

        $fieldset->addField('sort_order', 'text', ['name' => 'sort_order', 'label' => __('Priority')]);

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => ['1' => __('Active'), '0' => __('Inactive')]
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField(
            'apply_to',
            'select',
            [
                'label' => __('Apply To'),
                'name' => 'apply_to',
                'required' => true,
                'options' => $this->_rule->getAppliesToOptions(true)
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From Date'),
                'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );
        $fieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('To Date'),
                'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );

        $fieldset->addField(
            'positions_limit',
            'text',
            [
                'name' => 'positions_limit',
                'label' => __('Result Limit'),
                'note' => __('Maximum number of products that can be matched by this Rule. Capped to 20.')
            ]
        );

        $this->_eventManager->dispatch(
            'targetrule_edit_tab_main_after_prepare_form',
            ['model' => $model, 'form' => $form, 'block' => $this]
        );

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Rule Information');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Rule Information');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
