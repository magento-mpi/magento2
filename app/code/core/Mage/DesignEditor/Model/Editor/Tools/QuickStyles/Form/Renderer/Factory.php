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
    //@TODO Concept of Renderer_Factory, responsibility, where to be used, dependencies
    /*

        Will be passed to constructor of new form elements?
            How to get $rendererBlockLayoutName then?
            And how to share it then?
            It will be better to set renderer to elements from parent elements cause parent elements know it better for which children renderer can be shared and when can't
            Do we need then to create super-class for all VDE elements or just composite ones?
                Tried to override VDE form elements' constructor in super-class and pass renderer factory,
                but elements are added to form in Varien_Data_Form_Abstract::addField() and are created without passing renderer factory


        Remember that we need to override renderers set by default in constructors and fieldset::addField() method
        Remember to remove previous renderers management code.

        ways:
        1. Renderer Factory passed to every element in constructor using DI
            class Vde_Element extends Varien_Data_Form_Element_Abstract
                public function __construct(Renderer_Factory $rendererFactory)

        2. Renderer Factory passed to 'composite' elements in constructor using DI.
            class Composite extends Varien_Data_Form_Element_Fieldset
                public function __construct(Renderer_Factory $rendererFactory)

        3. Renderer Factory passed to elements in constructor using $config
            $form->addField('id', 'font-selector', array('rendererFactory' => $rendererFactory));

        4. Renderer Factory is created to elements constructor (where we get $layout then?)
            class Vde_Element extends Varien_Data_Form_Element_Abstract
                public function __construct()
                {
                    $rendererFactory = new Renderer_Factory();
                    $this->_renderer = $rendererFactory->get(get_class($this));
                }
        5. Renderer passed to every element in constructor using $config
            $form->addField('id', 'font-selector', array('renderer' => $renderer));

        6. Before rendering form call method to recursively reset renderers to all elements

        7. Renderer factory passed to column by setRendererFactory() (so we not refactor all forms) and later factory passed using DI

        1 and 2 is impossible without refactoring Varien_Data_Form_Abstract::addField() cause element is created in

    */
    /**
     * Layout model
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    //@TODO remove references to Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column_Element and remove renderer itself
    protected $_rendererByElement = array(
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column',
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ColorPicker',
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite',
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font'
            //=> 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite',
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Font',
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_LogoUploader',
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite',
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker'
            => 'Mage_Backend_Block_Widget_Form_Renderer_Fieldset_Element',
        'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader'
            => 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_BackgroundUploader',
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
     */
    public function create($elementClassName, $rendererBlockLayoutName)
    {
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
