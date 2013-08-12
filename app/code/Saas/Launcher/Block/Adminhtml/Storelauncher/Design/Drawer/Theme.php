<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Drawer Theme Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme extends Magento_Backend_Block_Template
{
    /**
     * Get preview url for selected theme
     *
     * @param int $themeId
     * @return string
     */
    public function getPreviewUrl($themeId)
    {
        return $this->getUrl('adminhtml/system_design_editor/preview', array(
            'theme_id' => $themeId,
            'mode' => Mage_DesignEditor_Model_State::MODE_NAVIGATION
        ));
    }
}
