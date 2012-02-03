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
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        if (!$session->isDesignEditorActive()) {
            return $this;
        }
        if ($session->getSkin()) {
            Mage::getDesign()->setDesignTheme($session->getSkin());
        }
        return $this;
    }
}
