<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab;

use Magento\Backend\Block\Widget\Form;

/**
 * Reminder rules edit form general fields
 */
class General extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Store
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_store;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $store
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $store,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_store = $store;
    }

    /**
     * Prepare general properties form
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        $isEditable = $this->getCanEditReminderRule() !== false ? true : false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_reminder_rule');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'comment' => __(
                    'Reminder emails may promote a shopping cart price rule with or without a coupon. If a shopping cart price rule defines an auto-generated coupon, this reminder rule will generate a random coupon code for each customer.'
                )
            ]
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField('name', 'text', ['name' => 'name', 'label' => __('Rule Name'), 'required' => true]);

        $fieldset->addField(
            'description',
            'textarea',
            ['name' => 'description', 'label' => __('Description'), 'style' => 'height: 100px;']
        );

        $field = $fieldset->addField(
            'salesrule_id',
            'note',
            [
                'name' => 'salesrule_id',
                'label' => __('Shopping Cart Price Rule'),
                'class' => 'widget-option',
                'value' => $model->getSalesruleId(),
                'note' => __('Promotion rule this reminder will advertise.'),
                'readonly' => !$isEditable
            ]
        );

        $model->unsSalesruleId();
        $helperBlock = $this->getLayout()->createBlock('Magento\SalesRule\Block\Adminhtml\Promo\Widget\Chooser');

        if ($helperBlock instanceof \Magento\Framework\Object) {
            $helperBlock->setConfig(
                $this->getChooserConfig()
            )->setFieldsetId(
                $fieldset->getId()
            )->prepareElementHtml(
                $field
            );
        }

        if ($this->_storeManager->hasSingleStore()) {
            $websiteId = $this->_storeManager->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', ['name' => 'website_ids[]', 'value' => $websiteId]);
            $model->setWebsiteIds($websiteId);
        } else {
            $fieldset->addField(
                'website_ids',
                'multiselect',
                [
                    'name' => 'website_ids[]',
                    'label' => __('Assigned to Website'),
                    'title' => __('Assigned to Website'),
                    'required' => true,
                    'values' => $this->_store->getWebsiteValuesForForm(),
                    'value' => $model->getWebsiteIds()
                ]
            );
        }

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

        $dateFormat = $this->_localeDate->getDateFormat(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From Date'),
                'title' => __('From Date'),
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
                'title' => __('To Date'),
                'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );

        $fieldset->addField(
            'schedule',
            'text',
            [
                'name' => 'schedule',
                'label' => __('Repeat Schedule'),
                'note' => __(
                    'Enter the number of days before email reminder rule is triggered if conditions match (comma separate, e.g., "7, 14").'
                )
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        if (!$isEditable) {
            $this->getForm()->setReadonly(true, true);
        }

        return parent::_prepareForm();
    }

    /**
     * Get chooser config data
     *
     * @return array
     */
    public function getChooserConfig()
    {
        return ['button' => ['open' => __('Select Rule...')]];
    }
}
