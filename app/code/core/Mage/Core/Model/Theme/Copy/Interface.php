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
 * Interface for copy theme
 */
interface Mage_Core_Model_Theme_Copy_Interface
{
    /**
     * Create copy of theme associated with other theme
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     */
    public function copy($theme);
}
