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
 * A skin selector for design editor frontend toolbar panel
 */
class Mage_DesignEditor_Block_Toolbar_Skin extends Mage_Core_Block_Template
{
    /**
     * Html id of the skin select control
     */
    const VDE_HTML_SKIN_ID = 'visual_design_editor_skin';

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
     * Returns whether skin selected in current store design
     *
     * @param string $skin
     * @return bool
     */
    public function isSkinSelected($skin)
    {
        $currentSkin = Mage::getDesign()->getDesignTheme();
        return $currentSkin == $skin;
    }

    /**
     * Returns html id of the skin select control
     *
     * @return string
     */
    public function getSelectHtmlId()
    {
        return self::VDE_HTML_SKIN_ID;
    }
}
