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
 * Theme Interface
 */
interface Mage_Core_Model_ThemeInterface
{
    /**
     * Get parent theme
     *
     * @return Mage_Core_Model_ThemeInterface
     */
    public function getParentTheme();

    /**
     * Get theme path
     *
     * @return string
     */
    public function getThemePath();
}
