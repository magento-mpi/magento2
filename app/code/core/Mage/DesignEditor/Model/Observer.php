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
    /**
     * Renderer for wrapping html to be shown at frontend
     *
     * @var Mage_DesignEditor_Block_Wrapping
     */
    protected $_wrappingRenderer;

    /**
     * Applies custom skin to store, if design editor is active and custom skin is chosen
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_DesignEditor_Model_Observer
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyCustomSkin($observer)
    {
        $session = $this->_getSession();
        if (!$session->isDesignEditorActive()) {
            return $this;
        }
        if ($session->getSkin()) {
            Mage::getDesign()->setDesignTheme($session->getSkin());
        }
        return $this;
    }

    /**
     * Set design_editor_active flag, which allows to load DesignEditor's CSS or JS scripts
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_DesignEditor_Model_Observer
     */
    public function setDesignEditorFlag(Varien_Event_Observer $observer)
    {
        $block = $observer->getLayout()->getBlock('head');
        if (!$block) {
            return $this;
        }

        $session = $this->_getSession();
        if ($session->isDesignEditorActive()) {

            // disable using cache (no save, no load)
            Mage::app()->getCacheInstance()->banUse('block_html');

            $block->setDesignEditorActive(true);
        }
        return $this;
    }

    /**
     * Returns session for Magento Design Editor
     *
     * @return Mage_DesignEditor_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_DesignEditor_Model_Session');
    }

    /**
     * Puts markers with block info to show the block limits in html.
     * Works only if the Visual Design Editor is active.
     *
     * @return Mage_DesignEditor_Model_Observer
     */
    public function wrapHtmlWithBlockInfo(Varien_Event_Observer $observer)
    {
        if (!$this->_getSession()->isDesignEditorActive()) {
            return $this;
        }

        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        if ($this->_isBlockDraggable($block, $html)) {
            $html = $this->_wrapHtml($html, $block);
            $transport->setHtml($html);
        }

        return $this;
    }

    /**
     * Checks whether $html, produced by the $block, can be wrapped with draggable block markers
     *
     * @param Mage_Core_Block_Abstract $block
     * @param string $html
     * @return bool
     */
    public function _isBlockDraggable($block, $html)
    {
        $ownBlockPrefix = 'Mage_DesignEditor_Block_';
        if (strncmp(get_class($block), $ownBlockPrefix, strlen($ownBlockPrefix)) == 0) {
            return false;
        }
        // Markers cannot be placed outside the html body
        if (strpos($html, '<body') !== false) {
            return false;
        }
        $couldBeDraggable = false;
        // Markers should be placed only if block outputs real html, not some script or header tags
        if (preg_match('/<div|<p|<ul|<dt|<dl|<span|<b|<i|<a|<form|<h[\d]|<select|<input|<textarea/i', $html)
            || strlen(trim($html)) == 0
        ) {
            $couldBeDraggable = true;
        }
        return $couldBeDraggable && Mage::helper('Mage_DesignEditor_Helper_Data')->isBlockDraggable($block);
    }

    /**
     * Wraps block html with markers for frontend scripts
     *
     * @param string $html
     * @param Mage_Core_Block_Abstract $block
     * @return string
     */
    public function _wrapHtml($html, $block)
    {
        if (!$this->_wrappingRenderer) {
            $this->_wrappingRenderer = $block->getLayout()->getBlockSingleton('Mage_DesignEditor_Block_Wrapping');
        }
        return $this->_wrappingRenderer
            ->setWrappedHtml($html)
            ->setWrappedBlock($block)
            ->toHtml();
    }
}
