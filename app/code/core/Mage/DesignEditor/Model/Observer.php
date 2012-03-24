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
    const PAGE_HANDLE = 'design_editor_page';
    const TOOLBAR_HANDLE = 'design_editor_toolbar';

    /**
     * Renderer for wrapping html to be shown at frontend
     *
     * @var Mage_Core_Block_Template
     */
    protected $_wrappingRenderer = null;

    /**
     * Handler for 'controller_action_predispatch' event
     */
    public function preDispatch()
    {
        /* Deactivate the design editor, if the admin session has been already expired */
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_getSession()->deactivateDesignEditor();
        }
        /* Apply custom design to the current page */
        if ($this->_getSession()->isDesignEditorActive() && $this->_getSession()->getSkin()) {
            Mage::getDesign()->setDesignTheme($this->_getSession()->getSkin());
        }
    }

    /**
     * Add the design editor toolbar to the current page
     *
     * @param Varien_Event_Observer $observer
     */
    public function addToolbar(Varien_Event_Observer $observer)
    {
        if (!$this->_getSession()->isDesignEditorActive()) {
            return;
        }
        $layout = $observer->getEvent()->getLayout();
        if (in_array('ajax_index', $layout->getUpdate()->getHandles())) {
            $layout->getUpdate()->addHandle(self::PAGE_HANDLE);
        }
        $layout->getUpdate()->addHandle(self::TOOLBAR_HANDLE);
    }

    /**
     * Disable blocks HTML output caching
     */
    public function disableBlocksOutputCaching()
    {
        if (!$this->_getSession()->isDesignEditorActive()) {
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
        if (!$this->_getSession()->isDesignEditorActive()) {
            return;
        }
        /** @var $block Mage_Page_Block_Html_Head */
        $block = $observer->getEvent()->getLayout()->getBlock('head');
        if ($block) {
            $block->setDesignEditorActive(true);
        }
    }

    /**
     * Retrieve session instance for the design editor
     *
     * @return Mage_DesignEditor_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_DesignEditor_Model_Session');
    }

    /**
     * Wrap each element of a page that is being rendered, with a block-level HTML-element to hightligt it in VDE
     *
     * Subscriber to event 'core_layout_render_element'
     *
     * @param Varien_Event_Observer $observer
     */
    public function wrapPageElement(Varien_Event_Observer $observer)
    {
        if (!$this->_getSession()->isDesignEditorActive()) {
            return;
        }

        if (!$this->_wrappingRenderer) {
            $this->_wrappingRenderer = Mage::getModel('Mage_Core_Block_Template');
        }

        /** @var $structure Mage_Core_Model_Layout_Structure */
        $structure = $observer->getEvent()->getStructure();
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
        $name = $observer->getEvent()->getElementName();

        $block = $layout->getBlock($name);
        $isVdeToolbar = ($block && 0 === strpos(get_class($block), 'Mage_DesignEditor_Block_'));

        if ($structure->isManipulationAllowed($name) && !$isVdeToolbar) {
            $this->_wrappingRenderer
                ->setTemplate('Mage_DesignEditor::wrapping.phtml')
                ->setData(array('element_name' => $name, 'element_html' => $layout->getRenderingOutput()))
            ;
            $layout->setRenderingOutput($this->_wrappingRenderer->toHtml());
        }
    }

    /**
     * Deactivate the design editor
     */
    public function adminSessionUserLogout()
    {
        $this->_getSession()->deactivateDesignEditor();
    }
}
