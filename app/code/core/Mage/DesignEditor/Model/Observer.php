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
 * Observer for design editor module
 */
class Mage_DesignEditor_Model_Observer
{
    /**#@+
     * VDE specific layout update handles
     */
    const HANDLE_PAGE    = 'design_editor_page';
    const HANDLE_TOOLBAR = 'design_editor_toolbar';
    /**#@-*/

    /**
     * Renderer for wrapping html to be shown at frontend
     *
     * @var Mage_Core_Block_Template
     */
    protected $_wrappingRenderer = null;

    /**
     * Theme Factory
     *
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * Visual Design Editor session
     *
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_session;

    /**
     * System object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Module helper
     *
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * System logger
     *
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_DesignEditor_Model_Session $session
     * @param Magento_ObjectManager $objectManager
     * @param Mage_DesignEditor_Helper_Data $helper
     * @param Mage_Core_Model_Logger $logger
     */
    public function __construct(
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_DesignEditor_Model_Session $session,
        Magento_ObjectManager $objectManager,
        Mage_DesignEditor_Helper_Data $helper,
        Mage_Core_Model_Logger $logger
    ) {
        $this->_themeFactory = $themeFactory;
        $this->_session = $session;
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    /**
     * Handler for 'controller_action_predispatch' event
     *
     * @throws InvalidArgumentException
     */
    public function preDispatch()
    {
        /* Deactivate the design editor, if the admin session has been already expired */
        if (!$this->_session->isLoggedIn()) {
            $this->_session->deactivateDesignEditor();
        }

        $theme = null;
        /* Deactivate the design editor, if the theme cannot be loaded */
        if ($this->_session->isDesignEditorActive() || $this->_session->isDesignPreviewActive()) {
            try {
                $theme = $this->_themeFactory->create()->load($this->_session->getThemeId());
                if (!$theme->getId()) {
                    throw new InvalidArgumentException('The theme was not found.');
                }
                Mage::register('vde_theme', $theme);
            } catch (Exception $e) {
                $this->_session->deactivateDesignEditor();
                $this->_session->deactivateDesignPreview();
                $this->_logger->logException($e);
            }
        }

        /* Apply custom design to the current page */
        if ($theme) {
            Mage::getDesign()->setDesignTheme($theme);
        }
    }

    /**
     * Add the design editor toolbar to the current page
     *
     * @param Varien_Event_Observer $observer
     */
    public function addToolbar(Varien_Event_Observer $observer)
    {
        if (!$this->_session->isDesignEditorActive()) {
            return;
        }

        /** @var $update Mage_Core_Model_Layout_Merge */
        $update = $observer->getEvent()->getLayout()->getUpdate();
        $handles = $update->getHandles();
        $handle = reset($handles);
        if ($handle && $update->getPageHandleType($handle) == Mage_Core_Model_Layout_Merge::TYPE_FRAGMENT) {
            $update->addHandle(self::HANDLE_PAGE);
        }
        $update->addHandle(self::HANDLE_TOOLBAR);
    }

    /**
     * Disable blocks HTML output caching
     */
    public function disableBlocksOutputCaching()
    {
        if (!$this->_session->isDesignEditorActive()) {
            return;
        }
        Mage::app()->getCacheInstance()->banUse(Mage_Core_Block_Abstract::CACHE_GROUP);
    }

    /**
     * Set design_editor_active flag, which allows to load DesignEditor's CSS or JS scripts
     *
     * @param Varien_Event_Observer $observer
     */
    public function setDesignEditorFlag(Varien_Event_Observer $observer)
    {
        if (!$this->_session->isDesignEditorActive()) {
            return;
        }
        /** @var $block Mage_Page_Block_Html_Head */
        $block = $observer->getEvent()->getLayout()->getBlock('head');
        if ($block) {
            $block->setDesignEditorActive(true);
        }
    }

    /**
     * Wrap each element of a page that is being rendered, with a block-level HTML-element to highlight it in VDE
     *
     * Subscriber to event 'core_layout_render_element'
     *
     * @param Varien_Event_Observer $observer
     */
    public function wrapPageElement(Varien_Event_Observer $observer)
    {
        if (!$this->_session->isDesignEditorActive()) {
            return;
        }

        if (!$this->_wrappingRenderer) {
            $this->_wrappingRenderer = $this->_objectManager->create(
                'Mage_DesignEditor_Block_Template',
                array('data' => array('template' => 'wrapping.phtml'))
            );
        }

        $event = $observer->getEvent();
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $event->getData('layout');
        $elementName = $event->getData('element_name');
        /** @var $transport Varien_Object */
        $transport = $event->getData('transport');

        $block = $layout->getBlock($elementName);
        $isVde = ($block && 0 === strpos(get_class($block), 'Mage_DesignEditor_Block_'));
        $manipulationAllowed = $layout->isManipulationAllowed($elementName) && !$isVde;
        $isContainer = $layout->isContainer($elementName);

        if ($manipulationAllowed || $isContainer) {
            $elementId = 'vde_element_' . rtrim(strtr(base64_encode($elementName), '+/', '-_'), '=');
            $this->_wrappingRenderer->setData(array(
                'element_id'    => $elementId,
                'element_title' => $layout->getElementProperty($elementName, 'label') ?: $elementName,
                'element_html'  => $transport->getData('output'),
                'is_manipulation_allowed'  => $manipulationAllowed,
                'is_container'  => $isContainer,
                'element_name'  => $elementName,
            ));
            $transport->setData('output', $this->_wrappingRenderer->toHtml());
        }

        /* Inject toolbar at the very beginning of the page */
        if ($elementName == 'after_body_start') {
            $elementHtml = $transport->getData('output');
            $toolbarHtml = $layout->renderElement('design_editor_toolbar');
            $transport->setData('output', $toolbarHtml . $elementHtml);
        }
    }

    /**
     * Deactivate the design editor
     */
    public function adminSessionUserLogout()
    {
        $this->_session->deactivateDesignEditor();
    }
}
