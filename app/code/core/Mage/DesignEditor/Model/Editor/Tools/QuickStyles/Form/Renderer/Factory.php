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
 * Block that renders JS tab
 *
 * @method Mage_Core_Model_Theme getTheme()
 * @method setTheme($theme)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory
{
    /**
     * Layout model
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    protected $_rendererByElement = array(
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ColorPicker',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Font',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_LogoUploader',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker'
            => 'Mage_Backend_Block_Widget_Form_Renderer_Fieldset_Element',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_BackgroundUploader',

        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ImageUploader',

        'Varien_Data_Form_Element_Checkbox'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Checkbox'
    );

    /**
     * Storage of renderers that could be shared between elements
     *
     * @see self::create()
     * @var array
     */
    protected $_sharedRenderers = array();

    /**
     * @param Mage_Core_Model_Layout $layout
     */
    public function __construct(Mage_Core_Model_Layout $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * Get renderer for element
     *
     * @param string $elementClassName
     * @param string $rendererBlockLayoutName
     * @return Varien_Data_Form_Element_Renderer_Interface
     * @throws Mage_Core_Exception
     */
    public function create($elementClassName, $rendererBlockLayoutName)
    {
        if (!isset($this->_rendererByElement[$elementClassName])) {
            throw new Mage_Core_Exception(
                sprintf('No renderer registered for elements of class "%s"', $elementClassName)
            );
        }
        $rendererClass = $this->_rendererByElement[$elementClassName];
        $renderer = $this->_layout->createBlock($rendererClass, $rendererBlockLayoutName);

        return $renderer;
    }

    /**
     * Renderer can be shared if it's guaranteed that no nested elements that use this renderer again.
     * For example:
     *   If Renderer01 used to render Element01 that should render some other Element02 using same Renderer01 it will
     *   cause an error. Cause internal Renderer01 property '_element' will be overwritten with Element02 during
     *   reuse of renderer and then will not be restored.
     */
    public function getSharedInstance($elementClassName, $rendererBlockLayoutName = null)
    {
        $rendererClass = $this->_rendererByElement[$elementClassName];
        if (isset($this->_sharedRenderers[$rendererClass])) {
            $renderer = $this->_sharedRenderers[$rendererClass];
        } else {
            if ($rendererBlockLayoutName === null) {
                $rendererBlockLayoutName = uniqid('renderer-');
            }
            $renderer = $this->create($elementClassName, $rendererBlockLayoutName);
        }

        return $renderer;
    }
}
