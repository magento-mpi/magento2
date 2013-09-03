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
 * Block that renders JS tab
 *
 * @method Magento_Core_Model_Theme getTheme()
 * @method setTheme($theme)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory
{
    /**
     * Layout model
     *
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * List of form elements and renderers for them
     *
     * @var array
     */
    protected $_rendererByElement = array(
        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ColorPicker',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Font',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_LogoUploader',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker'
            => 'Magento_Backend_Block_Widget_Form_Renderer_Fieldset_Element',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_BackgroundUploader',

        'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ImageUploader',

        '\Magento\Data\Form\Element\Checkbox'
            => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Checkbox'
    );

    /**
     * Storage of renderers that could be shared between elements
     *
     * @see self::create()
     * @var array
     */
    protected $_sharedRenderers = array();

    /**
     * @param Magento_Core_Model_Layout $layout
     */
    public function __construct(Magento_Core_Model_Layout $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * Get renderer for element
     *
     * @param string $elementClassName
     * @param string $rendererName
     * @return \Magento\Data\Form\Element\Renderer\RendererInterface
     * @throws Magento_Core_Exception
     */
    public function create($elementClassName, $rendererName)
    {
        if (!isset($this->_rendererByElement[$elementClassName])) {
            throw new Magento_Core_Exception(
                sprintf('No renderer registered for elements of class "%s"', $elementClassName)
            );
        }
        $rendererClass = $this->_rendererByElement[$elementClassName];
        $renderer = $this->_layout->createBlock($rendererClass, $rendererName);

        return $renderer;
    }

    /**
     * Renderer can be shared if it's guaranteed that no nested elements that use this renderer again.
     * For example:
     *   If Renderer01 used to render Element01 that should render some other Element02 using same Renderer01 it will
     *   cause an error. Cause internal Renderer01 property '_element' will be overwritten with Element02 during
     *   reuse of renderer and then will not be restored.
     */
    public function getSharedInstance($elementClassName, $rendererName = null)
    {
        $rendererClass = $this->_rendererByElement[$elementClassName];
        if (isset($this->_sharedRenderers[$rendererClass])) {
            $renderer = $this->_sharedRenderers[$rendererClass];
        } else {
            if ($rendererName === null) {
                $rendererName = uniqid('renderer-');
            }
            $renderer = $this->create($elementClassName, $rendererName);
        }

        return $renderer;
    }
}
