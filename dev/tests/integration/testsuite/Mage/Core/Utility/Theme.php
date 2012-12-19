<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core theme utility
 */
class Mage_Core_Utility_Theme
{
    /**
     * Retrieve theme instance
     *
     * @param string $themePath
     * @param string|null $area
     * @return Mage_Core_Model_Theme
     */
    public static function getTheme($themePath, $area)
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getSingleton('Mage_Core_Model_Theme');
        $collection = $theme->getCollection()
            ->addFieldToFilter('theme_path', $themePath)
            ->addFieldToFilter('area', $area)
            ->load();
        return $collection->getFirstItem();
    }
}
