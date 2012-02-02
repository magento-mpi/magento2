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
}
