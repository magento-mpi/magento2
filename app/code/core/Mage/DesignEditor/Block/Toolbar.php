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
 * Frontend toolbar panel for the design editor controls
 */
class Mage_DesignEditor_Block_Toolbar extends Mage_Core_Block_Template
{
    /**
     * Prevent rendering if the design editor is inactive
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        if (!$session->isDesignEditorActive()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get exit editor URL
     *
     * @return string
     */
    public function getExitUrl()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Url')->getUrl('adminhtml/system_design_editor/exit');
    }

    /**
     * Returns messages for Visual Design Editor, clears list of session messages
     *
     * @return array
     */
    public function getMessages()
    {
        return Mage::getSingleton('Mage_DesignEditor_Model_Session')
            ->getMessages(true)
            ->getItems();
    }
}
