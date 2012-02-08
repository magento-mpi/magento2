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
     * Returns string with JSON configuration for skin selector
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $encodedUrl = Mage::helper('Mage_Core_Helper_Url')->getEncodedUrl();
        $config = array(
            'selectId' => $this->getSelectHtmlId(),
            'changeSkinUrl' => $this->getUrl('design/editor/skin'),
            'backParams' => array(
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $encodedUrl
            )
        );
        return Mage::helper('Mage_Core_Helper_Data')
            ->jsonEncode($config);
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
