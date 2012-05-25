<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend form widget
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Form extends Mage_Backend_Block_Widget
{

    /**
     * Form Object
     *
     * @var Varien_Data_Form
     */
    protected $_form;

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Mage_Backend::widget/form.phtml');
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Form_Renderer_Element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Form_Renderer_Fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Form_Renderer_Fieldset_Element')
        );

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    /**
     * Set form object
     *
     * @param Varien_Data_Form $form
     * @return Mage_Backend_Block_Widget_Form
     */
    public function setForm(Varien_Data_Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl(Mage::getBaseUrl());
        return $this;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        return $this;
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        $this->_initFormValues();
        return parent::_beforeToHtml();
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        return $this;
    }

    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude=array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if ( ($inputType = $attribute->getFrontend()->getInputType())
                 && !in_array($attribute->getAttributeCode(), $exclude)
                 && ('media_image' != $inputType)
                 ) {

                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                    array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => $attribute->getFrontend()->getLabel(),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $attribute->getNote(),
                    )
                )
                ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } else if ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } else if ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }

    /**
     * Add new element type
     *
     * @param Varien_Data_Form_Abstract $baseElement
     */
    protected function _addElementTypes(Varien_Data_Form_Abstract $baseElement)
    {
        $types = $this->_getAdditionalElementTypes();
        foreach ($types as $code => $className) {
            $baseElement->addType($code, $className);
        }
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array();
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }

}
