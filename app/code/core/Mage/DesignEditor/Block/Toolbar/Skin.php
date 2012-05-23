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
     * Returns list of skins in the system
     *
     * @return array
     */
    public function getOptions()
    {
        return Mage::getModel('Mage_Core_Model_Design_Source_Design')
            ->getOptions();
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
