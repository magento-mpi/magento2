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
    extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Store Manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Application locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Store
     *
     * @var Magento_Core_Model_System_Store
     */
    protected $_store;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_System_Store $store
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_System_Store $store,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
        $this->_store = $store;
    }

    /**
     * Prepare general properties form
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $isEditable = ($this->getCanEditReminderRule() !== false) ? true : false;
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_reminder_rule');

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

        if ($this->_storeManager->hasSingleStore()) {
            $websiteId = $this->_storeManager->getStore(true)->getWebsiteId();
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
                'values'   => $this->_store->getWebsiteValuesForForm(),
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

        $dateFormat = $this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);

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
            'button' => array('open' => __('Select Rule...'))
        );
    }
}
