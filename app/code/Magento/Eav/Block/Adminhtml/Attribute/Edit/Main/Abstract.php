<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product attribute add/edit form main tab
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
    extends Magento_Backend_Block_Widget_Form_Generic
{
    protected $_attribute = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Eav data
     *
     * @var Magento_Eav_Helper_Data
     */
    protected $_eavData = null;

    /**
     * @var Magento_Eav_Model_Entity_Attribute_Config
     */
    protected $_attributeConfig;
    
    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Eav_Helper_Data $eavData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Eav_Model_Entity_Attribute_Config $attributeConfig
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Eav_Helper_Data $eavData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Eav_Model_Entity_Attribute_Config $attributeConfig,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_eavData = $eavData;
        $this->_attributeConfig = $attributeConfig;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    public function setAttributeObject($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * @return Magento_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeObject()
    {
        if (null === $this->_attribute) {
            return $this->_coreRegistry->registry('entity_attribute');
        }
        return $this->_attribute;
    }

    /**
     * Preparing default form elements for editing attribute
     *
     * @return Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->getAttributeObject();

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => __('Attribute Properties'))
        );

        if ($attributeObject->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $yesno = Mage::getModel('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray();

        $labels = $attributeObject->getFrontendLabel();
        $fieldset->addField(
            'attribute_label',
            'text',
            array(
                'name' => 'frontend_label[0]',
                'label' => __('Attribute Label'),
                'title' => __('Attribute Label'),
                'required' => true,
                'value' => is_array($labels) ? $labels[0] : $labels
            )
        );


        $validateClass = sprintf('validate-code validate-length maximum-length-%d',
            Magento_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH);
        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => __('Attribute Code'),
            'title' => __('Attribute Code'),
            'note'  => __('For internal use. Must be unique with no spaces. Maximum length of attribute code must be less than %1 symbols', Magento_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH),
            'class' => $validateClass,
            'required' => true,
        ));

        $inputTypes = Mage::getModel('Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype')->toOptionArray();

        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => __('Catalog Input Type for Store Owner'),
            'title' => __('Catalog Input Type for Store Owner'),
            'value' => 'text',
            'values'=> $inputTypes
        ));

        $fieldset->addField(
            'is_required',
            'select',
            array(
                'name' => 'is_required',
                'label' => __('Values Required'),
                'title' => __('Values Required'),
                'values' => $yesno,
            )
        );

        $fieldset->addField('default_value_text', 'text', array(
            'name' => 'default_value_text',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));

        $fieldset->addField('default_value_yesno', 'select', array(
            'name' => 'default_value_yesno',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'values' => $yesno,
            'value' => $attributeObject->getDefaultValue(),
        ));

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('default_value_date', 'date', array(
            'name'   => 'default_value_date',
            'label'  => __('Default Value'),
            'title'  => __('Default Value'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'value'  => $attributeObject->getDefaultValue(),
            'date_format' => $dateFormat
        ));

        $fieldset->addField('default_value_textarea', 'textarea', array(
            'name' => 'default_value_textarea',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));

        $fieldset->addField(
            'is_unique',
            'select',
            array(
                'name' => 'is_unique',
                'label' => __('Unique Value'),
                'title' => __('Unique Value (not shared with other products)'),
                'note' => __('Not shared with other products'),
                'values' => $yesno,
            )
        );

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => __('Input Validation for Store Owner'),
            'title' => __('Input Validation for Store Owner'),
            'values'=> $this->_eavData->getFrontendClasses(
                $attributeObject->getEntityType()->getEntityTypeCode()
            )
        ));

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
     */
    protected function _initFormValues()
    {
        $this->_eventManager->dispatch('adminhtml_block_eav_attribute_edit_form_init', array(
            'form' => $this->getForm(),
        ));
        $this->getForm()
            ->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $attributeObject = $this->getAttributeObject();
        if ($attributeObject->getId()) {
            $form = $this->getForm();
            foreach ($this->_attributeConfig->getLockedFields($attributeObject) as $field) {
                if ($element = $form->getElement($field)) {
                    $element->setDisabled(1);
                    $element->setReadonly(1);
                }
            }
        }
        return $this;
    }

    /**
     * Processing block html after rendering
     * Adding js block to the end of this block
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $jsScripts = $this->getLayout()
            ->createBlock('Magento_Eav_Block_Adminhtml_Attribute_Edit_Js')->toHtml();
        return $html . $jsScripts;
    }
}
