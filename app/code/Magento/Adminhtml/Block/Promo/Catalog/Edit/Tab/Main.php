<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Rule General Information Tab
 *
 * @category Magento
 * @package Magento_Adminhtml
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main
    extends Magento_Adminhtml_Block_Widget_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $formFactory, $data);
    }

    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Rule Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_catalog_rule');

        $form = $this->_createForm();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => __('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Rule Name'),
            'title' => __('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => __('Active'),
                '0' => __('Inactive'),
            ),
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'     => __('Websites'),
                'title'     => __('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => __('Customer Groups'),
            'title'     => __('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('Magento_Customer_Model_Resource_Group_Collection')->toOptionArray()
        ));

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => __('From Date'),
            'title'  => __('From Date'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => __('To Date'),
            'title'  => __('To Date'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => __('Priority'),
        ));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
        Mage::dispatchEvent('adminhtml_promo_catalog_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
