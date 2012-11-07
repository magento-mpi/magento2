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
 * A theme selector for design editor frontend toolbar panel
 */
class Mage_DesignEditor_Block_Toolbar_Theme extends Mage_Core_Block_Template
{
    /**
     * Html id of the theme select control
     */
    const VDE_HTML_THEME_ID = 'visual_design_editor_theme';

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getTheme()
    {
        return Mage::registry('vde_theme');
    }

    /**
     * Returns whether theme selected in current store design
     *
     * @param string $theme
     * @return bool
     */
    public function isThemeSelected($theme)
    {
        $currentTheme = Mage::getDesign()->getDesignTheme();
        return $currentTheme == $theme;
    }

    /**
     * Returns html id of the theme select control
     *
     * @return string
     */
    public function getSelectHtmlId()
    {
        return self::VDE_HTML_THEME_ID;
    }
}
