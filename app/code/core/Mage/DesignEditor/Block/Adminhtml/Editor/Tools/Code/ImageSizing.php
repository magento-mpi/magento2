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
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     * @param Mage_Eav_Model_Config $eavConfig
     * @param Mage_DesignEditor_Model_Editor_Tools_Controls_Factory $controlFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        Mage_Eav_Model_Config $eavConfig,
        Mage_DesignEditor_Model_Editor_Tools_Controls_Factory $controlFactory,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_controlFactory = $controlFactory;
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $filesystem, $data);
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

        $fieldMessage = $this->__('Add the white borders to the images that do not match the container size.');
        $form->addField('add_white_borders', 'checkbox', array(
            'checked' => $this->getVar('add_white_borders', 'Mage_Catalog'),
            'after_element_html' => $fieldMessage
        ));


        $hintMessage =  $this->__('If an image goes beyond the container edges')
            . $this->__(', it will be re-scaled to match the container size.')
            . '<br />'
            . $this->__('By default, the white borders will be added to an image to fill in the container space.');
        $form->addField('add_white_borders_hint', 'note', array('after_element_html' => $hintMessage));


        $form->addType('image_sizing', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageSizing');
        $selectOptions = $this->_getSelectOptions();

        /** @var $controlsConfig Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration */
        $controlsConfig = $this->_controlFactory->create(
            Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_IMAGE_SIZING,
            $this->getTheme()
        );

        $controls = $controlsConfig->getAllControlsData();
        foreach ($controls as $name => $control ) {
            $defaultValues = $this->_getDefaultValues($control);
            $form->addField($name, 'image_sizing', array(
                'name'                 => $name,
                'label'                => $control['layoutParams']['title'],
                'title'                => $control['layoutParams']['title'],
                'select_options'       => $selectOptions,
                'value'                => $this->_getValues($control, $defaultValues),
                'default_values_event' => $this->_getDefaultValuesEvent($defaultValues, $name),
                'default_values_label' => $this->__('Reset to Original')
            ));
        }

        parent::_prepareForm();
        return $this;
    }

    /**
     * Get values by location
     *
     * @param array $element
     * @param array $defaultValues
     * @return array
     */
    protected function _getValues(array $element, array $defaultValues)
    {
        return array(
            'type'   => $element['components']['image-type']['value']?:$defaultValues['type'],
            'width'  => $element['components']['image-width']['value']?:$defaultValues['width'],
            'height' => $element['components']['image-height']['value']?:$defaultValues['height']
        );
    }

    /**
     * Get default values
     *
     * @param array $element
     * @return array
     */
    protected function _getDefaultValues(array $element)
    {
        return array(
            'type'     => $element['components']['image-type']['default'],
            'width'    => $element['components']['image-width']['default'],
            'height'   => $element['components']['image-height']['default']
        );
    }

    /**
     * Get json string for default values event
     *
     * @param array $defaultValues
     * @param $name
     * @return string
     */
    protected function _getDefaultValuesEvent(array $defaultValues, $name)
    {
        $eventData = array('location' => $name) + $defaultValues;
        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode(array('button' => array(
            'event'     => 'restoreDefaultData',
            'target'    => 'body',
            'eventData' => $eventData
        ))));
    }

    /**
     * Return values for select element
     *
     * @return array
     */
    protected function _getSelectOptions()
    {
        $options   = array();
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
}
