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
 * Block that renders Code tab (or Scripts tab)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code extends Mage_Core_Block_Template
{
    /**
     * Get tabs html
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            $this->getChildHtml('design_editor_tools_code_css'),
            $this->getChildHtml('design_editor_tools_code_js'),
            $this->getChildHtml('design_editor_tools_code_custom')
        );
    }
}
