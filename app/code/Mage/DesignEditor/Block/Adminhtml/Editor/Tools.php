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
 * Block that renders VDE tools panel
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools extends Mage_Core_Block_Template
{
    /**
     * Get tabs
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            $this->getChildHtml('design_editor_tools_quick-styles'),
            $this->getChildHtml('design_editor_tools_block'),
            $this->getChildHtml('design_editor_tools_settings'),
            $this->getChildHtml('design_editor_tools_code'),
        );
    }

    /**
     * Return theme identification number
     *
     * @return int|null
     */
    protected function getThemeId()
    {
        /** @var $helper Mage_DesignEditor_Helper_Data */
        $helper = $this->_helperFactory->get('Mage_DesignEditor_Helper_Data');
        return $helper->getEditableThemeId();
    }

    /**
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/saveQuickStyles',
            array('theme_id' => $this->getThemeId())
        );
    }
}
