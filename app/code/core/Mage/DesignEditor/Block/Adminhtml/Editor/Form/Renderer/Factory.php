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
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Factory
{
    //@TODO Concept of Renderer_Factory, responsibility, where to be used, dependencies
    /*

        Will be passed to constructor of new form elements?
            How to get $rendererBlockLayoutName then?
            And how to share it then?
            It will be better to set renderer to elements from parent elements cause parent elements know it better fow which children renderer can be shared and when can't
            Do we need then to create super-class for all VDE for elements? We could then override super-class contructor once and therefore pass renderer factory to all elements.

        Remember that we need to override renderers set by default in constructors and fieldset::addField() method
        Remember to remove previous renderers management code.

        ways:
        1. Renderer Factory passed to every element in constructor using DI. All elements are sub-classes of VDE_Form_Element_Abstract
        2. Renderer Factory passed to 'fieldset' elements in constructor using DI. All such elements are sub-classes of VDE_Form_Element_Fieldset
        3. Renderer Factory passed to elements in constructor using $config
        4. Renderer Factory is created to elements constructor (where we get $layout then?)
        5. Renderer passed to every element in constructor using $config

        1 and 2 is impossible cause element is created in Varien_Data_Form_Abstract::addField()

    */
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
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Model_Layout $layout
        //Mage_Core_Controller_Request_Http $request,
        //Mage_Core_Model_Event_Manager $eventManager,
        //Mage_Backend_Model_Url $urlBuilder,
        //Mage_Core_Model_Translate $translator,
        //Mage_Core_Model_Cache $cache,
        //Mage_Core_Model_Design_Package $designPackage,
        //Mage_Core_Model_Session $session,
        //Mage_Core_Model_Store_Config $storeConfig,
        //Mage_Core_Controller_Varien_Front $frontController,
        //Mage_Core_Model_Factory_Helper $helperFactory,
        //Mage_Core_Model_Dir $dirs,
        //Mage_Core_Model_Logger $logger,
        //Magento_Filesystem $filesystem,
        //Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Factory $formFactory,
        //array $data = array()
    ) {
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
