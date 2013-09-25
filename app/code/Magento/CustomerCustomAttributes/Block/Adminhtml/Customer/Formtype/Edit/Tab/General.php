<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form Type Edit General Tab Block
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Edit_Tab_General
    extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * @var Magento_Core_Model_Theme_LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Theme_LabelFactory $themeLabelFactory
     * @param Magento_Core_Model_System_Store $systemStore
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Theme_LabelFactory $themeLabelFactory,
        Magento_Core_Model_System_Store $systemStore,
        array $data = array()
    ) {
        $this->_themeLabelFactory = $themeLabelFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Initialize Edit Form
     */
    protected function _construct()
    {
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
        parent::_construct();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        /* @var $model Magento_Eav_Model_Form_Type */
        $model      = $this->_coreRegistry->registry('current_form_type');

        /** @var Magento_Data_Form $form */
        $form       = $this->_formFactory->create();
        $fieldset   = $form->addFieldset('general_fieldset', array(
            'legend'    => __('General Information')
        ));

        $fieldset->addField('continue_edit', 'hidden', array(
            'name'      => 'continue_edit',
            'value'     => 0
        ));
        $fieldset->addField('type_id', 'hidden', array(
            'name'      => 'type_id',
            'value'     => $model->getId()
        ));

        $fieldset->addField('form_type_data', 'hidden', array(
            'name'      => 'form_type_data'
        ));

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => __('Form Code'),
            'title'     => __('Form Code'),
            'required'  => true,
            'class'     => 'validate-code',
            'disabled'  => true,
            'value'     => $model->getCode()
        ));

        $fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     => __('Form Title'),
            'title'     => __('Form Title'),
            'required'  => true,
            'value'     => $model->getLabel()
        ));

        /** @var $label Magento_Core_Model_Theme_Label */
        $label = $this->_themeLabelFactory->create();
        $options = $label->getLabelsCollection();
        array_unshift($options, array(
            'label' => __('All Themes'),
            'value' => ''
        ));
        $fieldset->addField('theme', 'select', array(
            'name'      => 'theme',
            'label'     => __('For Theme'),
            'title'     => __('For Theme'),
            'values'    => $options,
            'value'     => $model->getTheme(),
            'disabled'  => true
        ));

        $fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => __('Store View'),
            'title'     => __('Store View'),
            'values'    => $this->_systemStore->getStoreValuesForForm(false, true),
            'value'     => $model->getStoreId(),
            'disabled'  => true
        ));

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
        return __('General');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General');
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
