<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block that renders Custom tab
 *
 * @method Mage_Core_Model_Theme getTheme()
 * @method setTheme($theme)
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing extends Mage_Backend_Block_Widget_Form
{
    /**
     * @var Mage_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Mage_DesignEditor_Model_Editor_Tools_Controls_Factory
     */
    protected $_controlFactory;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Eav_Model_Config $eavConfig
     * @param Mage_DesignEditor_Model_Editor_Tools_Controls_Factory $controlFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Eav_Model_Config $eavConfig,
        Mage_DesignEditor_Model_Editor_Tools_Controls_Factory $controlFactory,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_controlFactory = $controlFactory;
        parent::__construct($context, $data);
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'action'   => '#',
            'method'   => 'post'
        ));
        $form->setId('product_image_sizing_form');
        $this->setForm($form);
        $form->setUseContainer(true);
        $form->setFieldNameSuffix('imagesizing');
        $form->addType('button_button', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Button');

        try{
            /** @var $controlsConfig Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration */
            $controlsConfig = $this->_controlFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_IMAGE_SIZING,
                $this->getTheme()
            );

            $whiteBorder = $controlsConfig->getControlData('product_image_border');
            $controls = $controlsConfig->getAllControlsData();
        } catch (Magento_Exception $e) {
            $whiteBorder = array();
            $controls = array();
        }

        if ($whiteBorder) {
            $this->_addWhiteBorderElement($whiteBorder);
        }

        foreach ($controls as $name => $control ) {
            if ($control['type'] != 'image-sizing') {
                continue;
            }
            $this->_addImageSizeElement($name, $control);
        }

        $form->addField('save_image_sizing', 'button_button', array(
            'name'  => 'save_image_sizing',
            'title' => $this->__('Update'),
            'value' => $this->__('Update'),
            'class' => 'primary',
            'data-mage-init' => $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode(array(
                'button' => array(
                    'event'  => 'saveForm',
                    'target' => 'body'
                )
            )))
        ));

        parent::_prepareForm();
        return parent::_prepareForm();
    }

    /**
     * Add white border checkbox to form
     *
     * @param array $control
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addWhiteBorderElement($control)
    {
        /** @var $form Varien_Data_Form */
        $form = $this->getForm();
        $fieldMessage = $this->__('Add the white borders to the images that do not match the container size.');
        foreach ($control['components'] as $name => $component) {
            $form->addField('add_white_borders_hidden', 'hidden', array(
                'name'  => $name,
                'value' => '0'
            ));
            $form->addField('add_white_borders', 'checkbox', array(
                'name'    => $name,
                'checked' => !empty($component['value']),
                'value'   => '1',
                'after_element_html' => $fieldMessage
            ));
        }
        $hintMessage =  $this->__('If an image goes beyond the container edges,'
            . ' it will be re-scaled to match the container size.'
            . ' By default, the white borders will be added to an image to fill in the container space');
        $form->addField('add_white_borders_hint', 'note', array(
            'after_element_html' => '<p class="description">' . $hintMessage . '</p>'));

        return $this;
    }

    /**
     * Add one image sizing item to form
     *
     * @param string $name
     * @param array $control
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addImageSizeElement($name, $control)
    {
        /** @var $form Varien_Data_Form */
        $form = $this->getForm();
        $fieldset = $form->addFieldset($name, array(
            'name'   => $name,
            'fieldset_type' => 'field',
            'legend' =>  $control['layoutParams']['title']
        ));
        $this->_addElementTypes($fieldset);

        $defaultValues = array();
        foreach ($control['components'] as $componentName => $component) {
            $defaultValues[$componentName] = $component['default'];
            switch ($component['type']) {
                case 'image-type':
                    $fieldset->addField($componentName, 'select', array(
                        'name'   => $componentName,
                        'values' => $this->_getSelectOptions(),
                        'value'  => $this->_getValue($component)
                    ));
                    break;
                case 'image-width':
                    $fieldset->addField($componentName, 'text', array(
                        'name'   => $componentName,
                        'class'  => 'image-width',
                        'value'  => $this->_getValue($component),
                        'before_element_html' => '<span>W</span>'
                    ));
                    break;
                case 'image-ratio':
                    $fieldset->addField($componentName . '-hidden', 'hidden', array(
                        'name'  => $componentName,
                        'value' => '0'
                    ));
                    $fieldset->addField($componentName, 'checkbox', array(
                        'checked'=> $this->_getValue($component) ? 'checked' : false,
                        'name'   => $componentName,
                        'class'  => 'image-ratio',
                        'value'  => '1',
                        'after_element_html' => '<span class="action-connect"></span>'
                    ));
                    break;
                case 'image-height':
                    $fieldset->addField($componentName, 'text', array(
                        'name'   => $componentName,
                        'class'  => 'image-height',
                        'value'  => $this->_getValue($component),
                        'before_element_html' => '<span>H</span>'
                    ));
                    break;
            }
        }
        $fieldset->addField($name . '_reset', 'button_button', array(
            'name'  => $name . '_reset',
            'title' => $this->__('Reset to Original'),
            'value' => $this->__('Reset to Original'),
            'class' => 'action-reset',
            'data-mage-init' => $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode(array(
                'button' => array(
                    'event'     => 'restoreDefaultData',
                    'target'    => 'body',
                    'eventData' => $defaultValues
                )
        )))));

        return $this;
    }

    /**
     * Get value
     *
     * @param array $component
     * @return array
     */
    protected function _getValue($component)
    {
        return $component['value'] !== false ? $component['value'] : $component['default'];
    }

    /**
     * Return values for select element
     *
     * @return array
     */
    protected function _getSelectOptions()
    {
        $options = array();
        foreach ($this->getImageTypes() as $imageType) {
            $attribute = $this->_eavConfig->getAttribute('catalog_product', $imageType);
            $options[] = array(
                'value' => $imageType,
                'label' => $attribute->getFrontendLabel()
            );
        }
        return $options;
    }

    /**
     * Return product image types
     *
     * @return array
     */
    public function getImageTypes()
    {
        return array('image', 'small_image', 'thumbnail');
    }

    /**
     * Set additional form button
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array('button_button' => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Button');
    }
}
