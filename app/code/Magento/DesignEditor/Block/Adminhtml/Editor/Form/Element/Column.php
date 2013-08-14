<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer to Quick Styles panel in VDE
 *
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column setClass($class)
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column
    extends Magento_Data_Form_Element_Fieldset
    implements Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ContainerInterface
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'column';

    /**
     * @var Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory
     */
    protected $_rendererFactory;

    /**
     * @var Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory
     */
    protected $_elementsFactory;

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->_addElementTypes();
        $this->addClass(self::CONTROL_TYPE);
    }

    /**
     * Add element types that can be added to 'column' element
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column
     */
    protected function _addElementTypes()
    {
        //contains composite font element and logo uploader
        $this->addType('logo', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo');

        //contains font picker, color picker
        $this->addType('font', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font');

        //contains color picker and bg uploader
        $this->addType('background', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background');

        $this->addType('color-picker', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker');
        $this->addType('font-picker', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker');
        $this->addType('logo-uploader', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader');
        $this->addType('background-uploader',
            'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader'
        );

        return $this;
    }

    /**
     * @param Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory $factory
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column
     */
    public function setRendererFactory($factory)
    {
        $this->_rendererFactory = $factory;
        return $this;
    }

    /**
     * @return Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory
     * @throws Magento_Core_Exception
     */
    public function getRendererFactory()
    {
        if (!$this->_rendererFactory) {
            throw new Magento_Core_Exception('Renderer factory was not set');
        }
        return $this->_rendererFactory;
    }

    /**
     * @param Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory $factory
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column
     */
    public function setElementsFactory($factory)
    {
        $this->_elementsFactory = $factory;
        return $this;
    }

    /**
     * @return Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory
     * @throws Magento_Core_Exception
     */
    public function getElementsFactory()
    {
        if (!$this->_elementsFactory) {
            throw new Magento_Core_Exception('Form elements factory was not set');
        }
        return $this->_elementsFactory;
    }

    /**
     * Add fields to column element
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param boolean $after
     * @param boolean $isAdvanced
     * @return Magento_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after = false, $isAdvanced = false)
    {
        if (isset($this->_types[$type])) {
            $className = $this->_types[$type];
        } else {
            $className = 'Magento_Data_Form_Element_' . ucfirst(strtolower($type));
        }
        $element = $this->getElementsFactory()->create($className, $config);
        $element->setId($elementId);
        $this->addElement($element, $after);

        $layoutName = $element->getId() . '-renderer';
        $renderer = $this->getRendererFactory()->create($className, $layoutName);
        $element->setRenderer($renderer);
        $element->setAdvanced($isAdvanced);
        return $element;
    }
}
