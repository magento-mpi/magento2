<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Model to create 'virtual' copy of 'staging' theme
 */
class Mage_Core_Model_Theme_Copy_StagingToVirtual extends Mage_Core_Model_Theme_Copy_Abstract
{
    /**
     * Create 'staging' theme associated with current 'virtual' theme
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     */
    public function copy($theme)
    {
        $virtualTheme = $theme->getParentTheme();
        $this->_copyLayoutUpdates($theme, $virtualTheme);
        return $virtualTheme;
    }
}
