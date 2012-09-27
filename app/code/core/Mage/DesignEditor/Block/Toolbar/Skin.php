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
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getTheme()
    {
        return Mage::registry('vde_theme');
    }

    /**
     * Get collection installed themes
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getThemeList()
    {
        /** @var  $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getModel('Mage_Core_Model_Theme');
        return $themeModel->getCollection();
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
        return 'visual_design_editor_skin';
    }
}
