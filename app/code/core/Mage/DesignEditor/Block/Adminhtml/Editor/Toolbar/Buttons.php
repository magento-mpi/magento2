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
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons extends Mage_Backend_Block_Template
{
    /**
     * Get "View Layout" button URL
     *
     * @return string
     */
    public function getViewLayoutUrl()
    {
        return $this->getUrl('*/*/getLayoutUpdate');
    }

    /**
     * Get "Back" button URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    /**
     * Get "Switch Mode" button URL
     *
     * @return string
     */
    public function getSwitchModeUrl()
    {
        //TODO add url
    }
}
