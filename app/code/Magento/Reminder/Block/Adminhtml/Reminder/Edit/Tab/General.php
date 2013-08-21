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
 * Reminder rules edit form general fields
 */
class Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare general properties form
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $isEditable = ($this->getCanEditReminderRule() !== false) ? true : false;
        $form = new Magento_Data_Form();
        $model = Mage::registry('current_reminder_rule');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'  => __('General Information'),
            'comment' => __('Reminder emails may promote a shopping cart price rule with or without a coupon. If a shopping cart price rule defines an auto-generated coupon, this reminder rule will generate a random coupon code for each customer.'),
        ));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'     => 'name',
            'label'    => __('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'  => 'description',
            'label' => __('Description'),
            'style' => 'height: 100px;',
        ));

        $field = $fieldset->addField('salesrule_id', 'note', array(
            'name'  => 'salesrule_id',
            'label' => __('Shopping Cart Price Rule'),
            'class' => 'widget-option',
            'value' => $model->getSalesruleId(),
            'note'  => __('Promotion rule this reminder will advertise.'),
            'readonly' => !$isEditable
        ));

        $model->unsSalesruleId();
        $helperBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Promo_Widget_Chooser');

        if ($helperBlock instanceof Magento_Object) {
            $helperBlock->setConfig($this->getChooserConfig())
                ->setFieldsetId($fieldset->getId())
                ->prepareElementHtml($field);
        }

        if (Mage::app()->hasSingleStore()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'    => __('Assigned to Website'),
                'title'    => __('Assigned to Website'),
                'required' => true,
                'values'   => Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteValuesForForm(),
                'value'    => $model->getWebsiteIds()
            ));
        }

        $fieldset->addField('is_active', 'select', array(
            'label'    => __('Status'),
            'name'     => 'is_active',
            'required' => true,
            'options'  => array(
                '1' => __('Active'),
                '0' => __('Inactive'),
            ),
        ));

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);

        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => __('From Date'),
            'title'  => __('From Date'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => __('To Date'),
            'title'  => __('To Date'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat
        ));

        $fieldset->addField('schedule', 'text', array(
            'name' => 'schedule',
            'label' => __('Repeat Schedule'),
            'note' => __('Enter the number of days before email reminder rule is triggered if conditions match (comma separate, e.g., "7, 14").'),
        ));

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
        return array(
            'button' => array('open'=>'Select Rule...')
        );
    }
}
