<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WYSIWYG widget options form
 *
 * @category   Magento
 * @package    Magento_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Widget_Block_Adminhtml_Widget_Options extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Element type used by default if configuration is omitted
     * @var string
     */
    protected $_defaultElementType = 'text';

    /**
     * @var Magento_Widget_Model_Widget
     */
    protected $_widget;

    /**
     * @var Magento_Core_Model_Option_ArrayPool
     */
    protected $_sourceModelPool;

    /**
     * @param Magento_Core_Model_Option_ArrayPool $sourceModelPool
     * @param Magento_Widget_Model_Widget $widget
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Option_ArrayPool $sourceModelPool,
        Magento_Widget_Model_Widget $widget,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_sourceModelPool = $sourceModelPool;
        $this->_widget = $widget;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare Widget Options Form and values according to specified type
     *
     * widget_type must be set in data before
     * widget_values may be set before to render element values
     */
    protected function _prepareForm()
    {
        $this->getForm()->setUseContainer(false);
        $this->addFields();
        return $this;
    }

    /**
     * Form getter/instantiation
     *
     * @return Magento_Data_Form
     */
    public function getForm()
    {
        if ($this->_form instanceof Magento_Data_Form) {
            return $this->_form;
        }
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $this->setForm($form);
        return $form;
    }

    /**
     * Fieldset getter/instantiation
     *
     * @return Magento_Data_Form_Element_Fieldset
     */
    public function getMainFieldset()
    {
        if ($this->_getData('main_fieldset') instanceof Magento_Data_Form_Element_Fieldset) {
            return $this->_getData('main_fieldset');
        }
        $mainFieldsetHtmlId = 'options_fieldset' . md5($this->getWidgetType());
        $this->setMainFieldsetHtmlId($mainFieldsetHtmlId);
        $fieldset = $this->getForm()->addFieldset($mainFieldsetHtmlId, array(
            'legend'    => __('Widget Options'),
            'class'     => 'fieldset-wide',
        ));
        $this->setData('main_fieldset', $fieldset);

        // add dependence javascript block
        $block = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Form_Element_Dependence');
        $this->setChild('form_after', $block);

        return $fieldset;
    }

    /**
     * Add fields to main fieldset based on specified widget type
     *
     * @throws Magento_Core_Exception
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    public function addFields()
    {
        // get configuration node and translation helper
        if (!$this->getWidgetType()) {
            throw new Magento_Core_Exception(__('Please specify a Widget Type.'));
        }
        $config = $this->_widget->getConfigAsObject($this->getWidgetType());
        if (!$config->getParameters()) {
            return $this;
        }
        foreach ($config->getParameters() as $parameter) {
            $this->_addField($parameter);
        }

        return $this;
    }

    /**
     * Add field to Options form based on parameter configuration
     *
     * @param Magento_Object $parameter
     * @return Magento_Data_Form_Element_Abstract
     */
    protected function _addField($parameter)
    {
        $form = $this->getForm();
        $fieldset = $this->getMainFieldset(); //$form->getElement('options_fieldset');

        // prepare element data with values (either from request of from default values)
        $fieldName = $parameter->getKey();
        $data = array(
            'name'      => $form->addSuffixToName($fieldName, 'parameters'),
            'label'     => __($parameter->getLabel()),
            'required'  => $parameter->getRequired(),
            'class'     => 'widget-option',
            'note'      => __($parameter->getDescription()),
        );

        if ($values = $this->getWidgetValues()) {
            $data['value'] = (isset($values[$fieldName]) ? $values[$fieldName] : '');
        }
        else {
            $data['value'] = $parameter->getValue();
            //prepare unique id value
            if ($fieldName == 'unique_id' && $data['value'] == '') {
                $data['value'] = md5(microtime(1));
            }
        }

        // prepare element dropdown values
        if ($values = $parameter->getValues()) {
            // dropdown options are specified in configuration
            $data['values'] = array();
            foreach ($values as $option) {
                $data['values'][] = array(
                    'label' => __($option['label']),
                    'value' => $option['value']
                );
            }
        }
        // otherwise, a source model is specified
        elseif ($sourceModel = $parameter->getSourceModel()) {
            $data['values'] = $this->_sourceModelPool->get($sourceModel)->toOptionArray();
        }

        // prepare field type or renderer
        $fieldRenderer = null;
        $fieldType = $parameter->getType();
        // hidden element
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
        }
        // just an element renderer
        elseif ($fieldType && $this->_isClassName($fieldType)) {
            $fieldRenderer = $this->getLayout()->createBlock($fieldType);
            $fieldType = $this->_defaultElementType;
        }

        // instantiate field and render html
        $field = $fieldset->addField($this->getMainFieldsetHtmlId() . '_' . $fieldName, $fieldType, $data);
        if ($fieldRenderer) {
            $field->setRenderer($fieldRenderer);
        }

        // extra html preparations
        if ($helper = $parameter->getHelperBlock()) {
            $helperBlock = $this->getLayout()->createBlock($helper->getType(), '', array('data' => $helper->getData()));
            if ($helperBlock instanceof Magento_Object) {
                $helperBlock->setConfig($helper->getData())
                    ->setFieldsetId($fieldset->getId())
                    ->prepareElementHtml($field);
            }
        }

        // dependencies from other fields
        $dependenceBlock = $this->getChildBlock('form_after');
        $dependenceBlock->addFieldMap($field->getId(), $fieldName);
        if ($parameter->getDepends()) {
            foreach ($parameter->getDepends() as $from => $row) {
                $values = isset($row['values']) ? array_values($row['values']) : (string)$row['value'];
                $dependenceBlock->addFieldDependence($fieldName, $from, $values);
            }
        }

        return $field;
    }

    /**
     * Checks whether $fieldType is a class name of custom renderer, and not just a type of input element
     *
     * @param string $fieldType
     * @return bool
     */
    protected function _isClassName($fieldType)
    {
        return preg_match('/[A-Z]/', $fieldType) > 0;
    }
}
