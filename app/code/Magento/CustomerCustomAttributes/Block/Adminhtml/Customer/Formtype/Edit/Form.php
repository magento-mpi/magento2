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
 * Form Type Edit Form Block
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Edit_Form
    extends Magento_Backend_Block_Widget_Form_Generic
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
     * Retrieve current form type instance
     *
     * @return Magento_Eav_Model_Form_Type
     */
    protected function _getFormType()
    {
        return $this->_coreRegistry->registry('current_form_type');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Edit_Form
     */
    protected function _prepareForm()
    {
        $editMode = $this->_coreRegistry->registry('edit_mode');
        if ($editMode == 'edit') {
            $saveUrl = $this->getUrl('*/*/save');
            $showNew = false;
        } else {
            $saveUrl = $this->getUrl('*/*/create');
            $showNew = true;
        }
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $saveUrl,
                'method'    => 'post',
            ))
        );

        if ($showNew) {
            $fieldset = $form->addFieldset('base_fieldset', array(
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ));

            $options = $this->_getFormType()->getCollection()->toOptionArray();
            array_unshift($options, array(
                'label' => __('-- Please Select --'),
                'value' => ''
            ));
            $fieldset->addField('type_id', 'select', array(
                'name'      => 'type_id',
                'label'     => __('Based On'),
                'title'     => __('Based On'),
                'required'  => true,
                'values'    => $options
            ));

            $fieldset->addField('label', 'text', array(
                'name'      => 'label',
                'label'     => __('Form Label'),
                'title'     => __('Form Label'),
                'required'  => true,
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
                'values'    => $options
            ));

            $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => __('Store View'),
                'title'     => __('Store View'),
                'required'  => true,
                'values'    => $this->_systemStore->getStoreValuesForForm(false, true)
            ));

            $form->setValues($this->_getFormType()->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
