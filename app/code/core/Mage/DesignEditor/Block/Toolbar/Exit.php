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
 * Exit button control block
 */
class Mage_DesignEditor_Block_Toolbar_Exit extends Mage_Core_Block_Template
{
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
