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
     * Wraps html with block info, if the Design Editor is active
     *
     * @return Mage_DesignEditor_Model_Observer
     */
    public function wrapHtmlWithBlockInfo(Varien_Event_Observer $observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        /** @var $helper Mage_DesignEditor_Helper_Data */
        $helper = Mage::helper('Mage_DesignEditor_Helper_Data');

        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        if ($this->_isCanBeWrapped($block, $html)) {
            $html = '<br class="vde_marker" block_name="' . $helper->escapeHtml($block->getNameInLayout())
                . '" marker_type="start"/>' . $html . '<br class="vde_marker" marker_type="end"/>';
            $transport->setHtml($html);
        }

        return $this;
    }

    /**
     * Checks whether $html, produced by the $block, can be wrapped to show as draggable block at frontend
     *
     * @param Mage_Core_Block_Abstract $block
     * @param string $html
     * @param return bool
     */
    public function _isCanBeWrapped($block, $html)
    {
        $ownBlockPrefix = 'Mage_DesignEditor_Block_';
        if (strncmp(get_class($block), $ownBlockPrefix, strlen($ownBlockPrefix)) == 0) {
            return false;
        }
        if (strpos($html, '<body') !== false) {
            return false;
        }
        if (preg_match('/<div|<p|<ul|<dt|<dl|<blockquote/i', $html)) {
            return true;
        }
        return false;
    }
}
