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
 * Sales Order Create Form Abstract Block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Adminhtml_Block_Sales_Order_Create_Form_Abstract
    extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    /**
     * Data Form object
     *
     * @var Magento_Data_Form
     */
    protected $_form;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_formFactory = $formFactory;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
    }

    /**
     * Prepare global layout
     * Add renderers to Magento_Data_Form
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        Magento_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Widget_Form_Renderer_Element',
                $this->getNameInLayout() . '_element'
            )
        );
        Magento_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        Magento_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * Return Form object
     *
     * @return Magento_Data_Form
     */
    public function getForm()
    {
        if (is_null($this->_form)) {
            $this->_form = $this->_formFactory->create();
            $this->_prepareForm();
        }
        return $this->_form;
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    abstract protected function _prepareForm();

    /**
     * Return array of additional form element types by type
     *
     * @return array
     */
    protected function _getAdditionalFormElementTypes()
    {
        return array(
            'file'      => 'Magento_Adminhtml_Block_Customer_Form_Element_File',
            'image'     => 'Magento_Adminhtml_Block_Customer_Form_Element_Image',
            'boolean'   => 'Magento_Adminhtml_Block_Customer_Form_Element_Boolean',
        );
    }

    /**
     * Return array of additional form element renderers by element id
     *
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        return array(
            'region'    => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Customer_Edit_Renderer_Region'),
        );
    }

    /**
     * Add additional data to form element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    protected function _addAdditionalFormElementData(Magento_Data_Form_Element_Abstract $element)
    {
        return $this;
    }

    /**
     * Add rendering EAV attributes to Form element
     *
     * @param array|Magento_Data_Collection $attributes
     * @param Magento_Data_Form_Abstract $form
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    protected function _addAttributesToForm($attributes, Magento_Data_Form_Abstract $form)
    {
        // add additional form types
        $types = $this->_getAdditionalFormElementTypes();
        foreach ($types as $type => $className) {
            $form->addType($type, $className);
        }
        $renderers = $this->_getAdditionalFormElementRenderers();

        foreach ($attributes as $attribute) {
            /** @var $attribute Magento_Customer_Model_Attribute */
            $attribute->setStoreId($this->_sessionQuote->getStoreId());
            $inputType = $attribute->getFrontend()->getInputType();

            if ($inputType) {
                $element = $form->addField($attribute->getAttributeCode(), $inputType, array(
                    'name'      => $attribute->getAttributeCode(),
                    'label'     => __($attribute->getStoreLabel()),
                    'class'     => $attribute->getFrontend()->getClass(),
                    'required'  => $attribute->getIsRequired(),
                ));
                if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
                $element->setEntityAttribute($attribute);
                $this->_addAdditionalFormElementData($element);

                if (!empty($renderers[$attribute->getAttributeCode()])) {
                    $element->setRenderer($renderers[$attribute->getAttributeCode()]);
                }

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $element->setValues($attribute->getFrontend()->getSelectOptions());
                } else if ($inputType == 'date') {
                    $format = $this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
                    $element->setImage($this->getViewFileUrl('images/grid-cal.gif'));
                    $element->setDateFormat($format);
                }
            }
        }

        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return array();
    }
}
