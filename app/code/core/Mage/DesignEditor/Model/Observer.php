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
}
