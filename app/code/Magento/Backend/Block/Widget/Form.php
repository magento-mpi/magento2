<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend form widget
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Magento_Backend_Block_Widget_Form extends Magento_Backend_Block_Widget
{
    /**
     * Form Object
     *
     * @var \Magento\Data\Form
     */
    protected $_form;

    protected $_template = 'Magento_Backend::widget/form.phtml';

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        \Magento\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_Backend_Block_Widget_Form_Renderer_Element',
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Magento_Backend_Block_Widget_Form_Renderer_Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_Backend_Block_Widget_Form_Renderer_Fieldset_Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     *
     * @return \Magento\Data\Form
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
     * @param \Magento\Data\Form $form
     * @return Magento_Backend_Block_Widget_Form
     */
    public function setForm(\Magento\Data\Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl($this->_urlBuilder->getBaseUrl());
        return $this;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        return $this;
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Magento_Backend_Block_Widget_Form|Magento_Core_Block_Abstract
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
     * @return Magento_Backend_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        return $this;
    }

    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param \Magento\Data\Form\Element\Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude=array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Magento_Eav_Model_Entity_Attribute */
            if (!$this->_isAttributeVisible($attribute)) {
                continue;
            }
            if ( ($inputType = $attribute->getFrontend()->getInputType())
                 && !in_array($attribute->getAttributeCode(), $exclude)
                 && (('media_image' != $inputType) || ($attribute->getAttributeCode() == 'image'))
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

                $this->_applyTypeSpecificConfig($inputType, $element, $attribute);
            }
        }
    }

    /**
     * Check whether attribute is visible
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @return bool
     */
    protected function _isAttributeVisible(Magento_Eav_Model_Entity_Attribute $attribute)
    {
        return !(!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible()));
    }
    /**
     * Apply configuration specific for different element type
     *
     * @param string $inputType
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     */
    protected function _applyTypeSpecificConfig($inputType, $element, Magento_Eav_Model_Entity_Attribute $attribute)
    {
        switch ($inputType) {
            case 'select':
                $element->setValues($attribute->getSource()->getAllOptions(true, true));
                break;
            case 'multiselect':
                $element->setValues($attribute->getSource()->getAllOptions(false, true));
                $element->setCanBeEmpty(true);
                break;
            case 'date':
                $element->setImage($this->getViewFileUrl('images/grid-cal.gif'));
                $element->setDateFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
                break;
            case 'multiline':
                $element->setLineCount($attribute->getMultilineCount());
                break;
            default:
                break;
        }
    }

    /**
     * Add new element type
     *
     * @param \Magento\Data\Form\AbstractForm $baseElement
     */
    protected function _addElementTypes(\Magento\Data\Form\AbstractForm $baseElement)
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
     * Render additional element
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }
}
